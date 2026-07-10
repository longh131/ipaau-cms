<?php

namespace App\Filament\Resources\PageResource\Forms;

use App\Support\PageTemplate\GeneralSecondarySections;
use Filament\Forms;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Fieldset;
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
                ->description('页面头部（标题、摘要）+ 可自由组合的下方板块；样式与基本/治理正文页统一。')
                ->statePath('data')
                ->schema([
                    Fieldset::make('页面头部')
                        ->schema([
                            Forms\Components\TextInput::make('heading')
                                ->label('标题')
                                ->placeholder('例如：Member Resources')
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
                    Forms\Components\Repeater::make('sections')
                        ->label('页面板块')
                        ->helperText('从上到下依次排列；可添加富文本模块、FAQ 手风琴或新闻列表。')
                        ->schema([
                            Forms\Components\Select::make('type')
                                ->label('板块类型')
                                ->options(GeneralSecondarySections::TYPE_OPTIONS)
                                ->default(GeneralSecondarySections::TYPE_CONTENT_BLOCK)
                                ->required()
                                ->live()
                                ->columnSpanFull(),
                            ...collect(BodyBlockFormSchemas::generalSecondaryContentBlockFields())
                                ->map(fn ($field) => $field->visible(
                                    fn (Get $get): bool => $get('type') === GeneralSecondarySections::TYPE_CONTENT_BLOCK,
                                ))
                                ->all(),
                            ...collect(BodyBlockFormSchemas::faqFields())
                                ->map(fn ($field) => $field->visible(
                                    fn (Get $get): bool => $get('type') === GeneralSecondarySections::TYPE_FAQ,
                                ))
                                ->all(),
                            ...collect(BodyBlockFormSchemas::newsListFields())
                                ->map(fn ($field) => $field->visible(
                                    fn (Get $get): bool => $get('type') === GeneralSecondarySections::TYPE_NEWS_LIST,
                                ))
                                ->all(),
                        ])
                        ->itemLabel(function (array $state): ?string {
                            $type = (string) ($state['type'] ?? '');

                            return match ($type) {
                                GeneralSecondarySections::TYPE_CONTENT_BLOCK => '富文本模块：'.Str::limit(
                                    (string) ($state['title'] ?? strip_tags((string) ($state['content'] ?? ''))),
                                    24,
                                ),
                                GeneralSecondarySections::TYPE_FAQ => 'FAQ（'.count($state['items'] ?? []).' 项）',
                                GeneralSecondarySections::TYPE_NEWS_LIST => '新闻列表：'.Str::limit(
                                    (string) (($state['section_title'] ?? '') ?: (collect($state['items'] ?? [])->first()['title'] ?? '')),
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
