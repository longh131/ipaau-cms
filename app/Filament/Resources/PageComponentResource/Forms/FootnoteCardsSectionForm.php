<?php

namespace App\Filament\Resources\PageComponentResource\Forms;

use App\Filament\Forms\ImageUpload;
use Filament\Forms;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;

class FootnoteCardsSectionForm
{
    /**
     * @return array<int, Component>
     */
    public static function schema(): array
    {
        return [
            Section::make('脚注卡片')
                ->visible(fn (Get $get): bool => $get('component_type') === 'footnote-cards')
                ->statePath('data')
                ->schema([
                    Forms\Components\Repeater::make('items')
                        ->label('卡片列表')
                        ->helperText('最多 6 张卡片；填写链接后整卡可点击，可勾选显示箭头图标')
                        ->schema([
                            Forms\Components\TextInput::make('title')
                                ->label('标题')
                                ->required()
                                ->maxLength(120)
                                ->columnSpanFull(),
                            Forms\Components\TextInput::make('url')
                                ->label('链接')
                                ->placeholder('/articles/ 或 https://')
                                ->maxLength(2048)
                                ->columnSpanFull(),
                            ImageUpload::make(
                                'image_desktop',
                                'page-components/footnote-cards/desktop',
                                '桌面端图片',
                                '建议正方形（如 400×400），留空则无背景图',
                            ),
                            ImageUpload::make(
                                'image_mobile',
                                'page-components/footnote-cards/mobile',
                                '移动端图片',
                                '可选，留空则使用桌面端图片',
                            ),
                            Forms\Components\Toggle::make('show_arrow')
                                ->label('显示箭头')
                                ->helperText('有链接时可显示右侧箭头，如 FIND AN ACCOUNTANT')
                                ->inline(false)
                                ->columnSpanFull(),
                        ])
                        ->minItems(0)
                        ->maxItems(6)
                        ->reorderable()
                        ->addActionLabel('添加卡片')
                        ->columns(2)
                        ->columnSpanFull(),
                ])
                ->columnSpanFull(),
        ];
    }
}
