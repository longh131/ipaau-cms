<?php

namespace App\Filament\Resources\PageComponentResource\Forms;

use App\Filament\Forms\ImageUpload;
use Filament\Forms;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;

class TabbedContentSectionForm
{
    /**
     * @return array<int, Component>
     */
    public static function schema(): array
    {
        return [
            Section::make('选项卡内容')
                ->visible(fn (Get $get): bool => $get('component_type') === 'tabbed-content')
                ->statePath('data')
                ->schema([
                    Forms\Components\Repeater::make('tabs')
                        ->label('选项卡列表')
                        ->helperText('每项对应一个切换标签；左侧为文案与按钮，右侧为对应图片。至少填写「切换按钮名称」才会显示该卡。')
                        ->schema([
                            Forms\Components\TextInput::make('tab_label')
                                ->label('切换按钮名称')
                                ->required()
                                ->maxLength(80)
                                ->columnSpanFull(),
                            Forms\Components\TextInput::make('tagline')
                                ->label('小标题')
                                ->maxLength(255)
                                ->columnSpanFull(),
                            Forms\Components\TextInput::make('title')
                                ->label('大标题')
                                ->maxLength(255)
                                ->columnSpanFull(),
                            Forms\Components\Textarea::make('description')
                                ->label('内容')
                                ->rows(4)
                                ->columnSpanFull(),
                            Forms\Components\TextInput::make('button_label')
                                ->label('链接按钮名称')
                                ->maxLength(120),
                            Forms\Components\TextInput::make('button_url')
                                ->label('链接按钮地址')
                                ->placeholder('/articles/ 或 https://')
                                ->maxLength(2048),
                            ImageUpload::make(
                                'image',
                                'page-components/tabbed-content',
                                '右侧图片',
                                '切换到此卡时在右侧显示',
                            )->columnSpanFull(),
                        ])
                        ->minItems(0)
                        ->maxItems(8)
                        ->reorderable()
                        ->addActionLabel('添加选项卡')
                        ->columns(2)
                        ->columnSpanFull(),
                ])
                ->columnSpanFull(),
        ];
    }
}
