<?php

namespace App\Filament\Resources\PageResource\Forms;

use App\Support\PageTemplate\PageBodyBlocks;
use App\Support\RichContent;
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
                            Forms\Components\Select::make('title_align')
                                ->label('标题对齐')
                                ->options(PageBodyBlocks::TITLE_ALIGN_OPTIONS)
                                ->default('center')
                                ->visible(fn (Get $get): bool => $get('type') === PageBodyBlocks::TYPE_RICH_TEXT)
                                ->columnSpanFull(),
                            RichContent::configureFileAttachments(
                                Forms\Components\RichEditor::make('html')
                                    ->label('段落内容')
                                    ->visible(fn (Get $get): bool => $get('type') === PageBodyBlocks::TYPE_RICH_TEXT)
                                    ->columnSpanFull()
                                    ->toolbarButtons(RichContent::pageToolbar())
                                    ->helperText(RichContent::imageUploadHelperText()),
                            ),
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
                            BodyBlockFormSchemas::bodyBlockButtonsRepeater(),
                            ...collect(BodyBlockFormSchemas::tabsRepeaterFields())
                                ->map(fn ($field) => $field->visible(fn (Get $get): bool => $get('type') === PageBodyBlocks::TYPE_TABS))
                                ->all(),
                            ...collect(BodyBlockFormSchemas::carouselFields())
                                ->map(fn ($field) => $field->visible(fn (Get $get): bool => $get('type') === PageBodyBlocks::TYPE_CAROUSEL))
                                ->all(),
                            ...collect(BodyBlockFormSchemas::mediaSplitFields())
                                ->map(fn ($field) => $field->visible(fn (Get $get): bool => $get('type') === PageBodyBlocks::TYPE_MEDIA_SPLIT))
                                ->all(),
                            ...collect(BodyBlockFormSchemas::contentColumnsFields())
                                ->map(fn ($field) => $field->visible(fn (Get $get): bool => $get('type') === PageBodyBlocks::TYPE_CONTENT_COLUMNS))
                                ->all(),
                            ...collect(BodyBlockFormSchemas::faqFields())
                                ->map(fn ($field) => $field->visible(fn (Get $get): bool => $get('type') === PageBodyBlocks::TYPE_FAQ))
                                ->all(),
                            ...collect(BodyBlockFormSchemas::statsFields())
                                ->map(fn ($field) => $field->visible(fn (Get $get): bool => $get('type') === PageBodyBlocks::TYPE_STATS))
                                ->all(),
                            ...collect(BodyBlockFormSchemas::cardListCuratedFields())
                                ->map(fn ($field) => $field->visible(fn (Get $get): bool => $get('type') === PageBodyBlocks::TYPE_CARD_LIST_CURATED))
                                ->all(),
                            ...collect(BodyBlockFormSchemas::newsListFields())
                                ->map(fn ($field) => $field->visible(fn (Get $get): bool => $get('type') === PageBodyBlocks::TYPE_NEWS_LIST))
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
                                PageBodyBlocks::TYPE_CONTENT_COLUMNS => '左右分栏：'.Str::limit(
                                    (string) (($state['columns'][0]['title'] ?? '') ?: ($state['columns'][1]['title'] ?? '')),
                                    24,
                                ),
                                PageBodyBlocks::TYPE_FAQ => 'FAQ（'.count($state['items'] ?? []).' 项）',
                                PageBodyBlocks::TYPE_STATS => '数字统计（'.count($state['items'] ?? []).' 项）',
                                PageBodyBlocks::TYPE_CARD_LIST_CURATED => '精选卡片列表：'.Str::limit(
                                    (string) (($state['section_title'] ?? '') ?: (collect($state['items'] ?? [])->first()['title'] ?? '')),
                                    24,
                                ),
                                PageBodyBlocks::TYPE_NEWS_LIST => '新闻列表：'.Str::limit(
                                    (string) (($state['section_title'] ?? '') ?: (collect($state['items'] ?? [])->first()['title'] ?? '')),
                                    24,
                                ),
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
