<?php

namespace App\Filament\Resources\PageComponentResource\Forms;

use Filament\Forms;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;

class CpdIntroSectionForm
{
    /**
     * @return array<int, Component>
     */
    public static function schema(): array
    {
        return [
            Section::make('CPD 介绍内容')
                ->visible(fn (Get $get): bool => $get('component_type') === 'cpd-intro')
                ->statePath('data')
                ->schema([
                    Forms\Components\Textarea::make('content')
                        ->label('标题 HTML')
                        ->helperText(
                            '直接粘贴 HTML 源码（不再经富文本编辑器，避免 span / class 被剥离）。'
                            . '渐变字示例：<span class="text-gradient-pink-reverse">高质量线上 CPD</span>。'
                            . '可用 class：text-gradient-pink、-pink-reverse、-purple、-orange、-blue 及对应 -reverse。'
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
