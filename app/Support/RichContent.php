<?php

namespace App\Support;

use App\Filament\RichEditor\Plugins\InlineStylePlugin;
use Filament\Forms\Components\RichEditor\RichContentRenderer;

class RichContent
{
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
        if (blank($state)) {
            return '';
        }

        return RichContentRenderer::make($state)
            ->plugins(static::plugins())
            ->toHtml();
    }
}
