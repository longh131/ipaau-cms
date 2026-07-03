<?php

namespace App\Filament\Resources\PageComponentResource\Forms;

use App\Filament\Forms\ImageUpload;
use Filament\Forms;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;

class StatsSectionForm
{
    /**
     * @return array<int, Component>
     */
    public static function schema(): array
    {
        return [
            Section::make('数据统计')
                ->visible(fn (Get $get): bool => $get('component_type') === 'stats')
                ->statePath('data')
                ->schema([
                    Forms\Components\Repeater::make('items')
                        ->label('统计项')
                        ->helperText('最多 4 项；数字可填文字或上传图片，卡片为渐变背景')
                        ->schema([
                            Forms\Components\Radio::make('number_type')
                                ->label('数字展示方式')
                                ->options([
                                    'text' => '文字',
                                    'image' => '图片',
                                ])
                                ->default('text')
                                ->live()
                                ->afterStateUpdated(function (Set $set, ?string $state): void {
                                    if ($state === 'text') {
                                        $set('number_image', null);
                                    } else {
                                        $set('number', null);
                                    }
                                })
                                ->columnSpanFull(),
                            Forms\Components\TextInput::make('number')
                                ->label('数字')
                                ->placeholder('例如：60k+、13、6+')
                                ->maxLength(30)
                                ->visible(fn (Get $get): bool => ($get('number_type') ?? 'text') === 'text')
                                ->required(fn (Get $get): bool => ($get('number_type') ?? 'text') === 'text')
                                ->columnSpanFull(),
                            ImageUpload::make(
                                'number_image',
                                'page-components/stats',
                                '数字图片',
                                '用于替代文字数字，建议 PNG/SVG 透明底（如火焰图标）',
                            )
                                ->visible(fn (Get $get): bool => ($get('number_type') ?? 'text') === 'image')
                                ->required(fn (Get $get): bool => ($get('number_type') ?? 'text') === 'image')
                                ->columnSpanFull(),
                            Forms\Components\TextInput::make('title')
                                ->label('标题')
                                ->placeholder('例如：全球会员及学生')
                                ->maxLength(255)
                                ->columnSpanFull(),
                            Forms\Components\Textarea::make('content')
                                ->label('内容')
                                ->rows(3)
                                ->placeholder('补充说明，可选')
                                ->columnSpanFull(),
                        ])
                        ->minItems(0)
                        ->maxItems(4)
                        ->reorderable()
                        ->addActionLabel('添加统计项')
                        ->columnSpanFull(),
                ])
                ->columnSpanFull(),
        ];
    }
}
