<?php

namespace App\Filament\Resources\PageComponentResource\Forms;

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
                    Forms\Components\Textarea::make('content')
                        ->label('正文 HTML')
                        ->helperText('左侧说明文字，支持 HTML（如 <h1>、<p>、<ul><li>…</li></ul>）。右侧表单字段固定为：姓名、手机号、邮箱、公司、现任职务、第一高等学历。')
                        ->rows(10)
                        ->extraAttributes([
                            'class' => 'font-mono text-sm',
                            'spellcheck' => 'false',
                        ])
                        ->columnSpanFull(),
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
