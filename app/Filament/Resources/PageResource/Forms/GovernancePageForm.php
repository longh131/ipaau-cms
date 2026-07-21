<?php

namespace App\Filament\Resources\PageResource\Forms;

use App\Filament\Forms\ImageUpload;
use App\Support\PageTemplate\PageBodyBlocks;
use App\Support\PageTemplate\Templates\GovernancePageData;
use Filament\Forms;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Section;

class GovernancePageForm
{
    /**
     * @return array<int, Component>
     */
    public static function schema(): array
    {
        return [
            Section::make('治理页内容')
                ->description('依次配置：页面头部 → Bento 导航 → 正文模块 → 精选卡片列表。')
                ->statePath('data')
                ->schema([
                    Fieldset::make('页面头部')
                        ->schema([
                            Forms\Components\TextInput::make('heading')
                                ->label('标题')
                                ->placeholder('例如：Governance')
                                ->helperText('留空则使用上方「页面标题」')
                                ->maxLength(255)
                                ->columnSpanFull(),
                            Forms\Components\Textarea::make('summary')
                                ->label('摘要')
                                ->rows(4)
                                ->helperText('宽度与基本正文页一致（最大 978px），前台居中显示')
                                ->columnSpanFull(),
                        ])
                        ->columnSpanFull(),
                    Fieldset::make('Bento 导航卡片')
                        ->schema([
                            Forms\Components\Select::make('bento_style')
                                ->label('布局样式')
                                ->options(GovernancePageData::BENTO_STYLE_OPTIONS)
                                ->default(GovernancePageData::BENTO_STYLE_FIVE)
                                ->required()
                                ->columnSpanFull(),
                            Forms\Components\Repeater::make('bento_cards')
                                ->label('卡片')
                                ->schema([
                                    Forms\Components\TextInput::make('title')
                                        ->label('标题')
                                        ->required()
                                        ->maxLength(255)
                                        ->columnSpanFull(),
                                    ImageUpload::make(
                                        'image',
                                        'page-components/pages/governance-bento',
                                        '背景图',
                                        '可选；无图时使用浅灰底',
                                    )->columnSpanFull(),
                                    Forms\Components\TextInput::make('url')
                                        ->label('链接')
                                        ->placeholder('/category/ 或 https://')
                                        ->maxLength(2048)
                                        ->columnSpan(1),
                                    Forms\Components\Select::make('target')
                                        ->label('打开方式')
                                        ->options([
                                            '' => '当前窗口',
                                            '_blank' => '新窗口',
                                        ])
                                        ->default('')
                                        ->columnSpan(1),
                                ])
                                ->minItems(0)
                                ->maxItems(10)
                                ->reorderable()
                                ->addActionLabel('添加卡片')
                                ->columns(2)
                                ->columnSpanFull(),
                        ])
                        ->columnSpanFull(),
                    Fieldset::make('正文模块')
                        ->schema([
                            Forms\Components\TextInput::make('content_title')
                                ->label('标题')
                                ->placeholder('例如：Strategic Plan')
                                ->maxLength(255)
                                ->columnSpanFull(),
                            Forms\Components\Select::make('content_title_align')
                                ->label('标题对齐')
                                ->options(PageBodyBlocks::TITLE_ALIGN_OPTIONS)
                                ->default('left')
                                ->columnSpanFull(),
                            Forms\Components\Textarea::make('content_body')
                                ->label('正文（HTML 源码）')
                                ->rows(8)
                                ->helperText('支持 HTML；对应前台 data-type="basicContentWithColumns" 单栏内容')
                                ->columnSpanFull()
                                ->extraInputAttributes([
                                    'class' => 'font-mono text-sm',
                                    'spellcheck' => 'false',
                                ]),
                            Forms\Components\TextInput::make('content_button_label')
                                ->label('按钮文字')
                                ->maxLength(120)
                                ->columnSpan(1),
                            Forms\Components\TextInput::make('content_button_url')
                                ->label('按钮链接')
                                ->placeholder('https:// 或 /category/...')
                                ->maxLength(2048)
                                ->columnSpan(1),
                            Forms\Components\Select::make('content_button_target')
                                ->label('按钮打开方式')
                                ->options([
                                    '' => '当前窗口',
                                    '_blank' => '新窗口',
                                ])
                                ->default('')
                                ->columnSpanFull(),
                        ])
                        ->columns(2)
                        ->columnSpanFull(),
                    Fieldset::make('精选卡片列表')
                        ->schema([
                            Forms\Components\TextInput::make('card_list_title')
                                ->label('板块标题')
                                ->placeholder('例如：Annual Reports')
                                ->maxLength(255)
                                ->columnSpanFull(),
                            Forms\Components\Repeater::make('card_list_items')
                                ->label('卡片')
                                ->helperText('前台默认显示 3 条，其余通过「查看更多」展开；每行 3 个卡片')
                                ->schema([
                                    Forms\Components\TextInput::make('title')
                                        ->label('标题')
                                        ->required()
                                        ->maxLength(255)
                                        ->columnSpanFull(),
                                    Forms\Components\TextInput::make('url')
                                        ->label('链接')
                                        ->placeholder('https:// 或 /category/...')
                                        ->maxLength(2048)
                                        ->columnSpan(1),
                                    Forms\Components\Select::make('target')
                                        ->label('打开方式')
                                        ->options([
                                            '' => '当前窗口',
                                            '_blank' => '新窗口',
                                        ])
                                        ->default('_blank')
                                        ->columnSpan(1),
                                ])
                                ->minItems(0)
                                ->maxItems(24)
                                ->reorderable()
                                ->addActionLabel('添加卡片')
                                ->columns(2)
                                ->columnSpanFull(),
                        ])
                        ->columnSpanFull(),
                ])
                ->columnSpanFull(),
        ];
    }
}
