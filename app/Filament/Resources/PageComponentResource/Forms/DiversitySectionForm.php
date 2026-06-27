<?php

namespace App\Filament\Resources\PageComponentResource\Forms;

use App\Filament\Forms\ImageUpload;
use Filament\Forms;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;

class DiversitySectionForm
{
    /**
     * @return array<int, Component>
     */
    public static function schema(): array
    {
        return [
            Section::make('多元与包容内容')
                ->visible(fn (Get $get): bool => $get('component_type') === 'diversity')
                ->statePath('data')
                ->schema([
                    ImageUpload::make(
                        'image',
                        'page-components/diversity',
                        '图片',
                        '显示在标题上方，建议宽图（如 1200×600），前台带圆角',
                    )->columnSpanFull(),
                    Forms\Components\Textarea::make('title')
                        ->label('标题 HTML')
                        ->helperText(
                            '直接粘贴 HTML 源码。请用 <h2> 包裹标题，渐变字放在 span 内。'
                            . '示例：<h2 class="diversity-section__heading"><span class="text-gradient-pink-reverse">以独立之思，缔造多元创新的会计未来。</span></h2>'
                        )
                        ->rows(10)
                        ->extraAttributes([
                            'class' => 'font-mono text-sm',
                            'spellcheck' => 'false',
                        ])
                        ->columnSpanFull(),
                ])
                ->columnSpanFull(),
        ];
    }
}
