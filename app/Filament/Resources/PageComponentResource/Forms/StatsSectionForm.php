<?php

namespace App\Filament\Resources\PageComponentResource\Forms;

use Filament\Forms;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;

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
                        ->helperText('最多 3 项；每项包含数字、标题与说明文字')
                        ->schema([
                            Forms\Components\TextInput::make('number')
                                ->label('数字')
                                ->required()
                                ->placeholder('例如：50k、100+')
                                ->maxLength(30)
                                ->columnSpanFull(),
                            Forms\Components\TextInput::make('title')
                                ->label('标题')
                                ->placeholder('例如：Members and Students globally')
                                ->maxLength(255)
                                ->columnSpanFull(),
                            Forms\Components\Textarea::make('content')
                                ->label('内容')
                                ->rows(3)
                                ->placeholder('补充说明，可选')
                                ->columnSpanFull(),
                        ])
                        ->minItems(0)
                        ->maxItems(3)
                        ->reorderable()
                        ->addActionLabel('添加统计项')
                        ->columnSpanFull(),
                ])
                ->columnSpanFull(),
        ];
    }
}
