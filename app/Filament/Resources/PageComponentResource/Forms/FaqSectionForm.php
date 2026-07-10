<?php

namespace App\Filament\Resources\PageComponentResource\Forms;

use Filament\Forms;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;

class FaqSectionForm
{
    /**
     * @return array<int, Component>
     */
    public static function schema(): array
    {
        return [
            Section::make('常见问题列表')
                ->visible(fn (Get $get): bool => $get('component_type') === 'faq')
                ->statePath('data')
                ->schema([
                    Forms\Components\Repeater::make('items')
                        ->label('问答项')
                        ->helperText('手风琴列表；至少填写问题，答案可选。可添加多项并拖拽排序。')
                        ->schema([
                            Forms\Components\TextInput::make('question')
                                ->label('问题')
                                ->required()
                                ->maxLength(500)
                                ->columnSpanFull(),
                            Forms\Components\RichEditor::make('answer')
                                ->label('答案')
                                ->columnSpanFull()
                                ->toolbarButtons(\App\Filament\Resources\PageResource\Forms\BodyBlockFormSchemas::faqAnswerToolbar()),
                        ])
                        ->minItems(0)
                        ->maxItems(30)
                        ->reorderable()
                        ->addActionLabel('添加问答')
                        ->columnSpanFull(),
                ])
                ->columnSpanFull(),
        ];
    }
}
