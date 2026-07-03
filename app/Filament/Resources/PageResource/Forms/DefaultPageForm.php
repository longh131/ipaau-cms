<?php

namespace App\Filament\Resources\PageResource\Forms;

use App\Support\PageTemplate\PageBodyBlocks;
use Filament\Forms;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Illuminate\Support\Str;

class DefaultPageForm
{
    /**
     * @return array<int, Component>
     */
    public static function schema(): array
    {
        return [
            Section::make('正文区块')
                ->description('页面内容由多个区块按顺序上下拼接显示，可自由组合段落、图文分栏、按钮、选项卡等。')
                ->statePath('data')
                ->schema([
                    Forms\Components\Repeater::make('body_blocks')
                        ->label('正文区块')
                        ->helperText('从上到下依次排列；拖拽可调整顺序。')
                        ->schema([
                            Forms\Components\Select::make('type')
                                ->label('区块类型')
                                ->options(PageBodyBlocks::TYPE_OPTIONS)
                                ->default(PageBodyBlocks::TYPE_RICH_TEXT)
                                ->required()
                                ->live()
                                ->columnSpanFull(),
                            Forms\Components\TextInput::make('title')
                                ->label('标题')
                                ->placeholder('例如：Who We Are')
                                ->helperText('可选；显示在正文上方，样式与 About 页章节标题一致')
                                ->maxLength(255)
                                ->visible(fn (Get $get): bool => $get('type') === PageBodyBlocks::TYPE_RICH_TEXT)
                                ->columnSpanFull(),
                            Forms\Components\RichEditor::make('html')
                                ->label('段落内容')
                                ->visible(fn (Get $get): bool => $get('type') === PageBodyBlocks::TYPE_RICH_TEXT)
                                ->columnSpanFull()
                                ->toolbarButtons([
                                    ['bold', 'italic', 'underline', 'strike'],
                                    ['h2', 'h3', 'blockquote'],
                                    ['bulletList', 'orderedList'],
                                    ['link', 'attachFiles'],
                                    ['undo', 'redo'],
                                    ['source-ai'],
                                ]),
                            Forms\Components\Textarea::make('text')
                                ->label('强调文字')
                                ->rows(3)
                                ->visible(fn (Get $get): bool => $get('type') === PageBodyBlocks::TYPE_HIGHLIGHT)
                                ->helperText('整句或关键词，前台以渐变样式显示')
                                ->columnSpanFull(),
                            Forms\Components\Select::make('gradient')
                                ->label('渐变样式')
                                ->options(PageBodyBlocks::GRADIENT_OPTIONS)
                                ->default('purple-reverse')
                                ->visible(fn (Get $get): bool => $get('type') === PageBodyBlocks::TYPE_HIGHLIGHT)
                                ->columnSpanFull(),
                            Forms\Components\Repeater::make('buttons')
                                ->label('按钮')
                                ->helperText('保存后，这一组按钮会作为独立一行显示在前后区块之间')
                                ->visible(fn (Get $get): bool => $get('type') === PageBodyBlocks::TYPE_CTA_GROUP)
                                ->schema([
                                    Forms\Components\TextInput::make('label')
                                        ->label('按钮文字')
                                        ->required()
                                        ->maxLength(120),
                                    Forms\Components\TextInput::make('url')
                                        ->label('链接')
                                        ->required()
                                        ->placeholder('https:// 或 /category/...')
                                        ->maxLength(2048),
                                    Forms\Components\Select::make('style')
                                        ->label('样式')
                                        ->options([
                                            'primary' => '蓝底白字（主按钮）',
                                            'secondary' => '白底蓝字（次按钮）',
                                        ])
                                        ->default('primary')
                                        ->required(),
                                    Forms\Components\Select::make('target')
                                        ->label('打开方式')
                                        ->options([
                                            '' => '当前窗口',
                                            '_blank' => '新窗口',
                                        ])
                                        ->default(''),
                                ])
                                ->minItems(1)
                                ->maxItems(6)
                                ->reorderable()
                                ->addActionLabel('添加按钮')
                                ->columns(2)
                                ->columnSpanFull(),
                            ...collect(BodyBlockFormSchemas::tabsRepeaterFields())
                                ->map(fn ($field) => $field->visible(fn (Get $get): bool => $get('type') === PageBodyBlocks::TYPE_TABS))
                                ->all(),
                            ...collect(BodyBlockFormSchemas::carouselFields())
                                ->map(fn ($field) => $field->visible(fn (Get $get): bool => $get('type') === PageBodyBlocks::TYPE_CAROUSEL))
                                ->all(),
                            ...collect(BodyBlockFormSchemas::mediaSplitFields())
                                ->map(fn ($field) => $field->visible(fn (Get $get): bool => $get('type') === PageBodyBlocks::TYPE_MEDIA_SPLIT))
                                ->all(),
                            ...collect(BodyBlockFormSchemas::faqFields())
                                ->map(fn ($field) => $field->visible(fn (Get $get): bool => $get('type') === PageBodyBlocks::TYPE_FAQ))
                                ->all(),
                            ...collect(BodyBlockFormSchemas::statsFields())
                                ->map(fn ($field) => $field->visible(fn (Get $get): bool => $get('type') === PageBodyBlocks::TYPE_STATS))
                                ->all(),
                        ])
                        ->itemLabel(function (array $state): ?string {
                            $type = (string) ($state['type'] ?? '');

                            return match ($type) {
                                PageBodyBlocks::TYPE_RICH_TEXT => '富文本：'.Str::limit(
                                    (string) ($state['title'] ?? strip_tags((string) ($state['html'] ?? ''))),
                                    24,
                                ),
                                PageBodyBlocks::TYPE_HIGHLIGHT => '渐变强调句：'.Str::limit((string) ($state['text'] ?? ''), 24),
                                PageBodyBlocks::TYPE_CTA_GROUP => '按钮组（'.count($state['buttons'] ?? []).' 个）',
                                PageBodyBlocks::TYPE_TABS => '选项卡（'.count($state['tabs'] ?? []).' 项）',
                                PageBodyBlocks::TYPE_CAROUSEL => '轮播：'.Str::limit((string) ($state['heading'] ?? '会员推荐'), 20),
                                PageBodyBlocks::TYPE_MEDIA_SPLIT => '图文分栏：'.Str::limit((string) ($state['title'] ?? ''), 20),
                                PageBodyBlocks::TYPE_FAQ => 'FAQ（'.count($state['items'] ?? []).' 项）',
                                PageBodyBlocks::TYPE_STATS => '数字统计（'.count($state['items'] ?? []).' 项）',
                                default => '正文区块',
                            };
                        })
                        ->minItems(0)
                        ->reorderable()
                        ->addActionLabel('添加区块')
                        ->collapsible()
                        ->columnSpanFull(),
                ])
                ->columnSpanFull(),
        ];
    }
}
