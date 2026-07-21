<?php

namespace App\Filament\Resources\PageResource\Forms;

use App\Support\PageTemplate\GeneralSecondarySections;
use App\Support\RichContent;
use Filament\Forms;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Illuminate\Support\Str;

class GeneralSecondaryPageForm
{
    /**
     * @return array<int, Component>
     */
    public static function schema(): array
    {
        return [
            Section::make('通用二级页内容')
                ->description('页面头部（标题、摘要、按钮）+ 可自由组合的下方板块；样式与基本/治理正文页统一。')
                ->statePath('data')
                ->schema([
                    Fieldset::make('页面头部')
                        ->schema([
                            Forms\Components\TextInput::make('heading')
                                ->label('标题')
                                ->placeholder('例如：Member Resources')
                                ->helperText('留空则前台不显示页面头部标题')
                                ->maxLength(255)
                                ->columnSpanFull(),
                            RichContent::nestedRichEditor(
                                'summary',
                                '摘要',
                                helperText: '留空则前台不显示；有内容时宽度与基本正文页一致（最大 978px），居中显示',
                            )->columnSpanFull(),
                            BodyBlockFormSchemas::sectionButtonsRepeater(),
                        ])
                        ->columnSpanFull(),
                    Forms\Components\Repeater::make('sections')
                        ->label('页面板块')
                        ->helperText('从上到下依次排列；可添加富文本、FAQ、新闻列表 A/B、数据统计、会员推荐、邮件订阅、左右结构、选项卡内容、图文分栏或 HTML 正文。')
                        ->schema([
                            Forms\Components\Select::make('type')
                                ->label('板块类型')
                                ->options(GeneralSecondarySections::TYPE_OPTIONS)
                                ->default(GeneralSecondarySections::TYPE_CONTENT_BLOCK)
                                ->required()
                                ->live()
                                ->columnSpanFull(),
                            Group::make()
                                ->schema(fn (Get $get): array => BodyBlockFormSchemas::generalSecondaryBlockFields($get('type')))
                                ->key(fn (Get $get): string => 'general-secondary-section-'.($get('type') ?? 'none'))
                                ->columnSpanFull(),
                        ])
                        ->itemLabel(function (array $state): ?string {
                            $type = (string) ($state['type'] ?? '');

                            return match ($type) {
                                GeneralSecondarySections::TYPE_CONTENT_BLOCK => '富文本模块：'.Str::limit(
                                    (string) ($state['title'] ?? strip_tags((string) ($state['content'] ?? ''))),
                                    24,
                                ),
                                GeneralSecondarySections::TYPE_FAQ => 'FAQ（'.count($state['items'] ?? []).' 项）',
                                GeneralSecondarySections::TYPE_NEWS_LIST_A => '新闻列表A：'.Str::limit(
                                    (string) (($state['section_title'] ?? '') ?: (collect($state['items'] ?? [])->first()['title'] ?? '')),
                                    24,
                                ),
                                GeneralSecondarySections::TYPE_NEWS_LIST => '新闻列表B：'.Str::limit(
                                    (string) (($state['section_title'] ?? '') ?: (collect($state['items'] ?? [])->first()['title'] ?? '')),
                                    24,
                                ),
                                GeneralSecondarySections::TYPE_STATS => '数据统计（'.count($state['items'] ?? []).' 项）',
                                GeneralSecondarySections::TYPE_TESTIMONIALS => '会员推荐：'.Str::limit(
                                    (string) (($state['section_title'] ?? '') ?: (collect($state['items'] ?? [])->first()['title'] ?? '')),
                                    24,
                                ),
                                GeneralSecondarySections::TYPE_NEWSLETTER => '邮件订阅：'.Str::limit(
                                    (string) ($state['title'] ?? ''),
                                    24,
                                ),
                                GeneralSecondarySections::TYPE_HTML_BODY => 'HTML 正文：'.Str::limit(
                                    strip_tags((string) ($state['body'] ?? '')),
                                    24,
                                ),
                                GeneralSecondarySections::TYPE_LEFT_RIGHT_LAYOUT => '左右结构：'.Str::limit(
                                    (string) (($state['title'] ?? '') ?: ($state['tagline'] ?? '')),
                                    24,
                                ),
                                GeneralSecondarySections::TYPE_TABBED_CONTENT => '选项卡内容（'.count($state['tabs'] ?? []).' 项）',
                                GeneralSecondarySections::TYPE_MEDIA_SPLIT => '图文分栏：'.Str::limit(
                                    (string) ($state['title'] ?? ''),
                                    24,
                                ),
                                default => '板块',
                            };
                        })
                        ->minItems(0)
                        ->reorderable()
                        ->addActionLabel('添加板块')
                        ->collapsible()
                        ->columnSpanFull(),
                ])
                ->columnSpanFull(),
        ];
    }
}
