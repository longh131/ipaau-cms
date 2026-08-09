<?php

namespace App\Filament\RichEditor\Plugins;

use App\Filament\RichEditor\Actions\AttachFilesWithLayoutAction;
use App\Filament\RichEditor\TipTap\ImageFloatExtension;
use Filament\Forms\Components\RichEditor\Plugins\Contracts\RichContentPlugin;
use Tiptap\Core\Extension;

class ImageFloatPlugin implements RichContentPlugin
{
    public static function make(): static
    {
        return app(static::class);
    }

    public function getId(): string
    {
        return 'image-float';
    }

    /**
     * @return array<string>
     */
    public function getTipTapJsExtensions(): array
    {
        return [
            asset('js/filament/rich-content-plugins/image-float.js'),
        ];
    }

    /**
     * @return array<Extension>
     */
    public function getTipTapPhpExtensions(): array
    {
        return [
            app(ImageFloatExtension::class),
        ];
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
        return [
            AttachFilesWithLayoutAction::make(),
        ];
    }
}
