<?php

namespace App\Support;

use App\Filament\Forms\StateCasts\JsonDocumentStateCast;
use App\Filament\RichEditor\Plugins\InlineStylePlugin;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\RichEditor\RichContentRenderer;

class RichContent
{
    /** 仅匹配真实换行；勿用 \R（会误伤 UTF-8 中文里的 0x85 字节，如「公」） */
    private const LINE_BREAK_PATTERN = '/\r\n|\n|\r/';

    private const PARAGRAPH_BREAK_PATTERN = '/(?:\r\n|\n|\r){2,}/';

    public static function fileAttachmentsDisk(): string
    {
        return 'public';
    }

    public static function fileAttachmentsDirectory(): string
    {
        return 'rich-editor';
    }

    public static function fileAttachmentsVisibility(): string
    {
        return 'public';
    }

    /**
     * @return array<int, array<int, string>>
     */
    public static function pageToolbar(): array
    {
        return [
            ['bold', 'italic', 'underline', 'strike'],
            ['h2', 'h3', 'h4', 'blockquote'],
            ['alignStart', 'alignCenter', 'alignEnd'],
            ['bulletList', 'orderedList'],
            ['link'],
            ['attachFiles'],
            ['undo', 'redo'],
            ['source-ai'],
        ];
    }

    public static function configureFileAttachments(RichEditor $editor): RichEditor
    {
        return $editor
            ->fileAttachments(true)
            ->resizableImages(true)
            ->fileAttachmentsDisk(static::fileAttachmentsDisk())
            ->fileAttachmentsDirectory(static::fileAttachmentsDirectory())
            ->fileAttachmentsVisibility(static::fileAttachmentsVisibility())
            ->getFileAttachmentUrlUsing(
                fn (?string $file): ?string => filled($file)
                    ? MediaUrl::toPublicStoragePath($file)
                    : null,
            );
    }

    /**
     * 用于 Repeater / 多层嵌套表单：Livewire 状态存 JSON 字符串，避免 TipTap 深层路径。
     */
    public static function nestedRichEditor(
        string $name,
        string $label,
        ?array $toolbar = null,
        ?string $helperText = null,
    ): RichEditor {
        $editor = static::configureFileAttachments(
            RichEditor::make($name)
                ->label($label)
                ->json()
                ->toolbarButtons($toolbar ?? static::pageToolbar())
                ->helperText($helperText ?? static::imageUploadHelperText()),
        );

        return $editor
            ->stateCast(fn (RichEditor $component): JsonDocumentStateCast => new JsonDocumentStateCast($component));
    }

    /**
     * 表单回填时将文档转为 JSON 字符串，配合 nestedRichEditor 使用。
     */
    public static function encodeDocumentForForm(mixed $state): ?string
    {
        $normalized = static::normalizeState($state);

        if ($normalized === null) {
            return null;
        }

        if (is_string($normalized)) {
            return $normalized;
        }

        return json_encode(static::normalizeDocument($normalized), JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
    }

    public static function imageUploadHelperText(): string
    {
        return '工具栏「插入图片」按钮可上传图片并插入正文';
    }

    /**
     * @return array<int, \Filament\Forms\Components\RichEditor\Plugins\Contracts\RichContentPlugin>
     */
    public static function plugins(): array
    {
        return [
            InlineStylePlugin::make(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function toDocument(mixed $state): array
    {
        if (is_array($state) && ($state['type'] ?? null) === 'doc') {
            return $state;
        }

        if (blank($state)) {
            return [
                'type' => 'doc',
                'content' => [
                    [
                        'type' => 'paragraph',
                        'content' => [],
                    ],
                ],
            ];
        }

        return RichContentRenderer::make($state)
            ->plugins(static::plugins())
            ->fileAttachmentsDisk(static::fileAttachmentsDisk())
            ->fileAttachmentsVisibility(static::fileAttachmentsVisibility())
            ->getEditor()
            ->getDocument();
    }

    /**
     * @param  array<string, mixed>  $document
     * @return array<string, mixed>
     */
    public static function normalizeDocument(array $document): array
    {
        if (($document['type'] ?? null) === 'doc') {
            $document = json_decode(json_encode($document), true);

            if (isset($document['content']) && is_array($document['content'])) {
                $document['content'] = array_map(
                    fn (mixed $node): mixed => is_array($node) ? static::normalizeNode($node) : $node,
                    $document['content'],
                );
            }

            return MediaUrl::normalizeRichContentValue($document);
        }

        return MediaUrl::normalizeRichContentValue(static::normalizeNode($document));
    }

    /**
     * @param  array<string, mixed>  $node
     * @return array<string, mixed>
     */
    protected static function normalizeNode(array $node): array
    {
        if (isset($node['attrs'])) {
            $attrs = $node['attrs'];

            if (! is_array($attrs) || array_is_list($attrs)) {
                unset($node['attrs']);
            } else {
                $style = trim((string) ($attrs['style'] ?? ''));
                $textAlign = $attrs['textAlign'] ?? null;

                if (filled($textAlign)) {
                    if (! str_contains($style, 'text-align')) {
                        $style = trim($style === '' ? '' : $style.'; ').'text-align: '.$textAlign;
                    }

                    unset($attrs['textAlign']);
                }

                if ($style !== '') {
                    $attrs['style'] = $style;
                } else {
                    unset($attrs['style']);
                }

                if ($attrs === []) {
                    unset($node['attrs']);
                } else {
                    $node['attrs'] = $attrs;
                }
            }
        }

        if (isset($node['content']) && is_array($node['content'])) {
            $node['content'] = array_map(
                fn (mixed $child): mixed => is_array($child) ? static::normalizeNode($child) : $child,
                $node['content'],
            );
        }

        if (isset($node['marks']) && is_array($node['marks'])) {
            $node['marks'] = array_map(function (mixed $mark): mixed {
                if (! is_array($mark)) {
                    return $mark;
                }

                if (isset($mark['attrs']) && (! is_array($mark['attrs']) || array_is_list($mark['attrs']))) {
                    unset($mark['attrs']);
                }

                return $mark;
            }, $node['marks']);
        }

        return $node;
    }

    /**
     * TipTap DOMSerializer requires node attrs to be objects; empty JSON arrays break rendering.
     *
     * @param  array<string, mixed>  $document
     * @return array<string, mixed>
     */
    protected static function documentForRenderer(array $document): array
    {
        return static::normalizeDocument($document);
    }

    public static function toHtml(mixed $state): string
    {
        $state = static::normalizeState($state);

        if (blank($state)) {
            return '';
        }

        if (is_string($state) && static::looksLikeHtml($state)) {
            return static::normalizeHtmlLineBreaks(MediaUrl::normalizeRichContentHtml($state));
        }

        if (is_string($state)) {
            return static::plainTextToHtml($state);
        }

        if (! is_array($state)) {
            return '';
        }

        return static::normalizeHtmlLineBreaks(
            MediaUrl::normalizeRichContentHtml(
                RichContentRenderer::make(static::documentForRenderer($state))
                    ->plugins(static::plugins())
                    ->fileAttachmentsDisk(static::fileAttachmentsDisk())
                    ->fileAttachmentsVisibility(static::fileAttachmentsVisibility())
                    ->processNodesUsing(function (object &$node): void {
                        if ($node->type !== 'image') {
                            return;
                        }

                        if (filled($node->attrs->id ?? null)) {
                            $node->attrs->id = MediaUrl::toRelativePath((string) $node->attrs->id);
                        }

                        if (filled($node->attrs->src ?? null)) {
                            $node->attrs->src = MediaUrl::normalizeRichContentUrl((string) $node->attrs->src);
                        } elseif (filled($node->attrs->id ?? null)) {
                            $node->attrs->src = MediaUrl::toPublicStoragePath($node->attrs->id);
                        }
                    })
                    ->toHtml()
            )
        );
    }

    protected static function plainTextToHtml(string $text): string
    {
        $paragraphs = preg_split(self::PARAGRAPH_BREAK_PATTERN, trim($text)) ?: [];

        if ($paragraphs === []) {
            return '';
        }

        return collect($paragraphs)
            ->map(function (string $paragraph): string {
                $paragraph = trim($paragraph);

                if ($paragraph === '') {
                    return '';
                }

                return '<p>'.nl2br(e($paragraph), false).'</p>';
            })
            ->filter()
            ->implode('');
    }

    protected static function normalizeHtmlLineBreaks(string $html): string
    {
        if (! preg_match(self::LINE_BREAK_PATTERN, $html)) {
            return $html;
        }

        return (string) preg_replace_callback(
            '/(<p(?:\s[^>]*)?>)(.*?)(<\/p>)/is',
            function (array $matches): string {
                $inner = $matches[2];

                if (! preg_match(self::LINE_BREAK_PATTERN, $inner) || preg_match('/<br\b/i', $inner)) {
                    return $matches[0];
                }

                $inner = preg_replace(self::LINE_BREAK_PATTERN, '<br>', $inner);

                return $matches[1].$inner.$matches[3];
            },
            $html
        );
    }

    public static function normalizeState(mixed $state): mixed
    {
        if (blank($state)) {
            return null;
        }

        if (is_array($state) && ($state['type'] ?? null) === 'doc') {
            return static::normalizeDocument($state);
        }

        if (! is_string($state)) {
            return $state;
        }

        $trimmed = trim($state);

        if ($trimmed === '') {
            return null;
        }

        if (str_starts_with($trimmed, '{') || str_starts_with($trimmed, '[')) {
            $decoded = json_decode($trimmed, true);

            if (is_array($decoded) && ($decoded['type'] ?? null) === 'doc') {
                return static::normalizeDocument($decoded);
            }

            if (is_array($decoded)) {
                return MediaUrl::normalizeRichContentValue($decoded);
            }
        }

        if (static::looksLikeHtml($trimmed)) {
            return MediaUrl::normalizeRichContentHtml($trimmed);
        }

        return $trimmed;
    }

    protected static function looksLikeHtml(string $value): bool
    {
        return (bool) preg_match('/<\s*(p|a|br|ul|ol|li|strong|em|span|div|h[1-6]|blockquote|img)\b/i', $value);
    }

    public static function hasVisibleHtml(?string $html): bool
    {
        if (blank($html)) {
            return false;
        }

        if (preg_match('/<img\b/i', $html)) {
            return true;
        }

        return filled(trim(strip_tags($html)));
    }
}
