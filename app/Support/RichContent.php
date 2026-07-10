<?php

namespace App\Support;

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
            ['h2', 'h3', 'blockquote'],
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
            ->fileAttachmentsVisibility(static::fileAttachmentsVisibility());
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
        if (($document['type'] ?? null) !== 'doc') {
            return $document;
        }

        $document = json_decode(json_encode($document), true);

        foreach ($document['content'] ?? [] as $index => $node) {
            if (! is_array($node)) {
                continue;
            }

            $type = $node['type'] ?? null;

            if (! in_array($type, ['heading', 'paragraph'], true)) {
                continue;
            }

            $attrs = is_array($node['attrs'] ?? null) ? $node['attrs'] : [];
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

            $document['content'][$index]['attrs'] = $attrs;
        }

        return $document;
    }

    public static function toHtml(mixed $state): string
    {
        $state = static::normalizeState($state);

        if (blank($state)) {
            return '';
        }

        if (is_string($state) && static::looksLikeHtml($state)) {
            return static::normalizeHtmlLineBreaks($state);
        }

        if (is_string($state)) {
            return static::plainTextToHtml($state);
        }

        return static::normalizeHtmlLineBreaks(
            RichContentRenderer::make($state)
                ->plugins(static::plugins())
                ->fileAttachmentsDisk(static::fileAttachmentsDisk())
                ->fileAttachmentsVisibility(static::fileAttachmentsVisibility())
                ->toHtml()
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
            return $state;
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

            if (is_array($decoded)) {
                return $decoded;
            }
        }

        return $trimmed;
    }

    protected static function looksLikeHtml(string $value): bool
    {
        return (bool) preg_match('/<\s*(p|a|br|ul|ol|li|strong|em|span|div|h[1-6]|blockquote|img)\b/i', $value);
    }
}
