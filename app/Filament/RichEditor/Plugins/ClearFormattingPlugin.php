<?php

namespace App\Filament\RichEditor\Plugins;

use Filament\Forms\Components\RichEditor\Plugins\Contracts\RichContentPlugin;

class ClearFormattingPlugin implements RichContentPlugin
{
    public static function make(): static
    {
        return app(static::class);
    }

    public function getId(): string
    {
        return 'clear-formatting';
    }

    /**
     * @return array<string>
     */
    public function getTipTapJsExtensions(): array
    {
        $path = public_path('js/filament/rich-content-plugins/clear-formatting.js');

        return [
            asset('js/filament/rich-content-plugins/clear-formatting.js').'?v='.(file_exists($path) ? filemtime($path) : '1'),
        ];
    }

    /**
     * @return array<\Tiptap\Core\Extension>
     */
    public function getTipTapPhpExtensions(): array
    {
        return [];
    }

    /**
     * @return array<\Filament\Forms\Components\RichEditor\RichEditorTool>
     */
    public function getEditorTools(): array
    {
        return [];
    }

    /**
     * @return array<\Filament\Actions\Action>
     */
    public function getEditorActions(): array
    {
        return [];
    }
}
