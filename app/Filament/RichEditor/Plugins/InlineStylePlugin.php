<?php

namespace App\Filament\RichEditor\Plugins;

use App\Filament\RichEditor\TipTap\DivNode;
use App\Filament\RichEditor\TipTap\GenericSpanMark;
use App\Filament\RichEditor\TipTap\InlineStyleExtension;
use App\Filament\RichEditor\TipTap\StyledLink;
use Filament\Forms\Components\RichEditor\Plugins\Contracts\RichContentPlugin;
use Filament\Support\Facades\FilamentAsset;
use Tiptap\Core\Extension;
use Tiptap\Core\Mark;

class InlineStylePlugin implements RichContentPlugin
{
    public static function make(): static
    {
        return app(static::class);
    }

    public function getId(): string
    {
        return 'inline-style';
    }

    /**
     * @return array<string>
     */
    public function getTipTapJsExtensions(): array
    {
        return [
            FilamentAsset::getScriptSrc('rich-content-plugins/inline-style'),
        ];
    }

    /**
     * @return array<Extension|Mark>
     */
    public function getTipTapPhpExtensions(): array
    {
        return [
            app(InlineStyleExtension::class),
            app(DivNode::class),
            app(GenericSpanMark::class),
            app(StyledLink::class, [
                'options' => [
                    'HTMLAttributes' => [],
                    'allowedProtocols' => [
                        'http', 'https', 'ftp', 'ftps', 'mailto', 'tel', 'callto', 'sms', 'cid', 'xmpp',
                    ],
                ],
            ]),
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
        return [];
    }
}
