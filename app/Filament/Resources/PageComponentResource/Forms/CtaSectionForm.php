<?php

namespace App\Filament\Resources\PageComponentResource\Forms;

use App\Filament\Forms\ImageUpload;
use Filament\Forms;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;

class CtaSectionForm
{
    /**
     * @return array<int, Component>
     */
    public static function schema(): array
    {
        return [
            Section::make('塑造未来内容')
                ->visible(fn (Get $get): bool => $get('component_type') === 'cta-section')
                ->statePath('data')
                ->schema([
                    ImageUpload::make(
                        'image',
                        'page-components/cta-section',
                        '图片',
                        '显示在左侧，建议竖向或方形配图（如 800×1000）',
                    )->columnSpanFull(),
                    Forms\Components\TextInput::make('tagline')
                        ->label('小标题')
                        ->placeholder('例如：shaping the future')
                        ->maxLength(255)
                        ->columnSpanFull(),
                    Forms\Components\Repeater::make('title_lines')
                        ->label('大标题')
                        ->helperText('每行对应前台一个 H3，可添加多行')
                        ->schema([
                            Forms\Components\TextInput::make('text')
                                ->label('标题行')
                                ->required()
                                ->maxLength(255),
                        ])
                        ->minItems(1)
                        ->maxItems(4)
                        ->reorderable()
                        ->addActionLabel('添加标题行')
                        ->columnSpanFull(),
                    Forms\Components\Textarea::make('description')
                        ->label('主要内容')
                        ->rows(6)
                        ->helperText('空行可分段显示为多个段落')
                        ->columnSpanFull(),
                    Forms\Components\Repeater::make('buttons')
                        ->label('按钮')
                        ->helperText('蓝底 = 主按钮，白底 = 次按钮；可添加多个')
                        ->schema([
                            Forms\Components\TextInput::make('label')
                                ->label('按钮文字')
                                ->required()
                                ->maxLength(120),
                            Forms\Components\TextInput::make('url')
                                ->label('链接')
                                ->required()
                                ->placeholder('https:// 或 /articles/')
                                ->maxLength(2048),
                            Forms\Components\Select::make('style')
                                ->label('样式')
                                ->options([
                                    'primary' => '蓝底白字（主按钮）',
                                    'secondary' => '白底蓝字（次按钮）',
                                ])
                                ->default('secondary')
                                ->required(),
                            Forms\Components\Select::make('target')
                                ->label('打开方式')
                                ->options([
                                    '' => '当前窗口',
                                    '_blank' => '新窗口',
                                ])
                                ->default(''),
                        ])
                        ->minItems(0)
                        ->maxItems(10)
                        ->reorderable()
                        ->addActionLabel('添加按钮')
                        ->columns(2)
                        ->columnSpanFull(),
                ])
                ->columnSpanFull(),
        ];
    }
}
