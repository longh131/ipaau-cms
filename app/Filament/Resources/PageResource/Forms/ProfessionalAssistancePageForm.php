<?php

namespace App\Filament\Resources\PageResource\Forms;

use App\Support\PageTemplate\ProfessionalAssistanceSections;
use Filament\Forms;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Illuminate\Support\Str;

class ProfessionalAssistancePageForm
{
    /**
     * @return array<int, Component>
     */
    public static function schema(): array
    {
        return [
            Section::make('专业协助页内容')
                ->description('可自由组合富文本段落、HTML 正文、新闻列表、图文分栏与轮播板块。')
                ->statePath('data')
                ->schema([
                    Forms\Components\Repeater::make('sections')
                        ->label('页面板块')
                        ->helperText('从上到下依次排列。')
                        ->schema([
                            Forms\Components\Select::make('type')
                                ->label('板块类型')
                                ->options(ProfessionalAssistanceSections::TYPE_OPTIONS)
                                ->default(ProfessionalAssistanceSections::TYPE_RICH_TEXT)
                                ->required()
                                ->live()
                                ->columnSpanFull(),
                            Group::make()
                                ->schema(fn (Get $get): array => BodyBlockFormSchemas::professionalAssistanceBlockFields($get('type')))
                                ->key(fn (Get $get): string => 'professional-assistance-section-'.($get('type') ?? 'none'))
                                ->columnSpanFull(),
                        ])
                        ->itemLabel(function (array $state): ?string {
                            $type = (string) ($state['type'] ?? '');

                            return match ($type) {
                                ProfessionalAssistanceSections::TYPE_RICH_TEXT => '富文本段落：'.Str::limit(
                                    (string) (($state['title'] ?? '') ?: strip_tags((string) ($state['html'] ?? ''))),
                                    24,
                                ),
                                ProfessionalAssistanceSections::TYPE_HTML_BODY => 'HTML 正文：'.Str::limit(
                                    (string) (($state['title'] ?? '') ?: ($state['tagline'] ?? '') ?: strip_tags((string) ($state['body'] ?? ''))),
                                    24,
                                ),
                                ProfessionalAssistanceSections::TYPE_NEWS_LIST_A => '新闻列表A：'.Str::limit(
                                    (string) (($state['section_title'] ?? '') ?: (collect($state['items'] ?? [])->first()['title'] ?? '')),
                                    24,
                                ),
                                ProfessionalAssistanceSections::TYPE_MEDIA_SPLIT => '图文分栏：'.Str::limit(
                                    (string) ($state['title'] ?? ''),
                                    24,
                                ),
                                ProfessionalAssistanceSections::TYPE_CAROUSEL => '轮播：'.Str::limit(
                                    (string) (($state['heading'] ?? '') ?: (collect($state['slides'] ?? [])->first()['author'] ?? '')),
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
