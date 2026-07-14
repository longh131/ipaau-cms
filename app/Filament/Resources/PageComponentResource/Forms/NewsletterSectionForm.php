<?php

namespace App\Filament\Resources\PageComponentResource\Forms;

use App\Support\RichContent;
use Filament\Forms;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;

class NewsletterSectionForm
{
    /**
     * @return array<int, Component>
     */
    public static function schema(): array
    {
        return [
            Section::make('邮件订阅内容')
                ->visible(fn (Get $get): bool => $get('component_type') === 'newsletter')
                ->statePath('data')
                ->schema([
                    Forms\Components\TextInput::make('title')
                        ->label('标题')
                        ->placeholder('例如：Subscribe to our newsletter')
                        ->maxLength(255)
                        ->columnSpanFull(),
                    RichContent::configureFileAttachments(
                        Forms\Components\RichEditor::make('content')
                            ->label('正文')
                            ->json()
                            ->toolbarButtons(RichContent::pageToolbar())
                            ->helperText('左侧说明文字；支持标题、列表、链接等。右侧表单字段固定为：姓名、手机号、邮箱、公司、现任职务、第一高等学历。')
                    )->columnSpanFull(),
                    Forms\Components\TextInput::make('button_text')
                        ->label('提交按钮文字')
                        ->default('提交')
                        ->maxLength(60)
                        ->columnSpanFull(),
                ])
                ->columnSpanFull(),
        ];
    }
}
