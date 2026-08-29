<?php



namespace App\Filament\Resources\PageResource\Forms;



use App\Support\PageTemplate\GeneralSecondarySections;

use App\Support\PageTemplate\GovernanceSections;

use App\Support\PageTemplate\PageBodyBlocks;

use Filament\Forms;

use Filament\Schemas\Components\Component;

use Filament\Schemas\Components\Fieldset;

use Filament\Schemas\Components\Group;

use Filament\Schemas\Components\Section;

use Filament\Schemas\Components\Utilities\Get;

use Illuminate\Support\Str;



class GovernancePageForm

{

    /**

     * @return array<int, Component>

     */

    public static function schema(): array

    {

        return [

            Section::make('治理倡导页内容')

                ->description('页面头部 + 可自由组合的下方板块（Bento、富文本、HTML 源码、精选卡片列表等）。')

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

                    Forms\Components\Repeater::make('sections')

                        ->label('页面板块')

                        ->helperText('从上到下依次排列；可按需添加 Bento 导航、富文本、HTML 源码或精选卡片列表。')

                        ->schema([

                            Forms\Components\Select::make('type')

                                ->label('板块类型')

                                ->options(GovernanceSections::TYPE_OPTIONS)

                                ->default(GovernanceSections::TYPE_BENTO)

                                ->required()

                                ->live()

                                ->columnSpanFull(),

                            Group::make()

                                ->schema(fn (Get $get): array => BodyBlockFormSchemas::governanceBlockFields($get('type')))

                                ->key(fn (Get $get): string => 'governance-section-'.($get('type') ?? 'none'))

                                ->columnSpanFull(),

                        ])

                        ->itemLabel(function (array $state): ?string {

                            $type = (string) ($state['type'] ?? '');



                            return match ($type) {

                                GovernanceSections::TYPE_BENTO => 'Bento 导航（'.count($state['cards'] ?? []).' 卡）',

                                GeneralSecondarySections::TYPE_CONTENT_BLOCK => '富文本模块：'.Str::limit(

                                    (string) ($state['title'] ?? strip_tags((string) ($state['content'] ?? ''))),

                                    24,

                                ),

                                GeneralSecondarySections::TYPE_HTML_BODY => 'HTML 正文：'.Str::limit(

                                    strip_tags((string) ($state['body'] ?? '')),

                                    24,

                                ),

                                PageBodyBlocks::TYPE_CARD_LIST_CURATED => '精选卡片：'.Str::limit(

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

