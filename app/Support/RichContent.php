<?php

namespace App\Support;

use App\Filament\RichEditor\Plugins\InlineStylePlugin;
use Filament\Forms\Components\RichEditor\RichContentRenderer;

class RichContent
{
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

        return RichContentRenderer::make($state)->getEditor()->getDocument();
    }

    public static function toHtml(mixed $state): string
    {
        if (blank($state)) {
            return '';
        }

        return RichContentRenderer::make($state)
            ->plugins([
                InlineStylePlugin::make(),
            ])
            ->toHtml();
    }
}
