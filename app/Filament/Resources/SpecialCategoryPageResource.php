<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SpecialCategoryPageResource\Pages;
use App\Models\Category;
use App\Models\Course;
use App\Models\SpecialCategoryPage;
use App\Support\CategoryListTemplate\CategoryListTemplateRegistry;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SpecialCategoryPageResource extends Resource
{
    protected static ?string $model = SpecialCategoryPage::class;

    protected static \BackedEnum|string|null $navigationIcon = Heroicon::Squares2x2;

    protected static ?string $navigationLabel = '功能栏目页';

    protected static ?int $navigationSort = 5;

    protected static string|\UnitEnum|null $navigationGroup = '内容管理';

    protected static ?string $modelLabel = '功能栏目页';

    protected static ?string $pluralModelLabel = '功能栏目页';

    /**
     * @return array<int, string>
     */
    public static function availableCategoryOptions(?int $ignoreRecordId = null): array
    {
        $usedCategoryIds = SpecialCategoryPage::query()
            ->when($ignoreRecordId, fn (Builder $query) => $query->whereKeyNot($ignoreRecordId))
            ->pluck('category_id')
            ->all();

        return Category::flatTreeSelectOptions(function (Category $category) use ($usedCategoryIds): bool {
            if (! $category->is_active || Course::isCourseCategory($category->id)) {
                return false;
            }

            return ! in_array($category->id, $usedCategoryIds, true);
        });
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\Select::make('category_id')
                    ->label('关联栏目')
                    ->options(fn (?SpecialCategoryPage $record): array => static::availableCategoryOptions($record?->getKey()))
                    ->required()
                    ->searchable()
                    ->disabled(fn (?SpecialCategoryPage $record): bool => $record !== null)
                    ->dehydrated()
                    ->helperText('页面标题与栏目介绍请在「栏目管理」中编辑。')
                    ->columnSpanFull(),
                Forms\Components\Select::make('feature_type')
                    ->label('功能类型')
                    ->options(SpecialCategoryPage::FEATURE_TYPE_OPTIONS)
                    ->default(SpecialCategoryPage::FEATURE_COURSE_LIST)
                    ->required()
                    ->live()
                    ->columnSpanFull(),
                Forms\Components\Placeholder::make('page_content_hint')
                    ->label('页面结构说明')
                    ->content(fn (Get $get): string => match ($get('feature_type')) {
                        SpecialCategoryPage::FEATURE_CERTIFICATE_LOOKUP => '前台顺序：栏目标题 → 栏目介绍 → 正文（HTML 源码·上） → 证书查询 → 正文（HTML 源码·下）',
                        SpecialCategoryPage::FEATURE_VIDEO_HUB => '前台顺序：栏目标题 → 栏目介绍 → 正文（HTML 源码·上） → IPA播报 / IPA活动回顾 视频区块 → 正文（HTML 源码·下）',
                        SpecialCategoryPage::FEATURE_CPD_RECORDS => '前台顺序：栏目标题 → 栏目介绍 → 正文（HTML 源码·上） → 查询学分证明按钮 → 正文（HTML 源码·下）',
                        default => '前台顺序：栏目标题 → 栏目介绍 → 正文（HTML 源码·上） → 课程表格（分页） → 正文（HTML 源码·下）',
                    })
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('body_html_top')
                    ->label('正文（HTML 源码）')
                    ->rows(16)
                    ->helperText(fn (Get $get): string => match ($get('feature_type')) {
                        SpecialCategoryPage::FEATURE_CERTIFICATE_LOOKUP => '显示在证书查询模块上方；直接粘贴或编写 HTML，保存后前台原样渲染。',
                        SpecialCategoryPage::FEATURE_VIDEO_HUB => '显示在视频汇总模块上方；直接粘贴或编写 HTML，保存后前台原样渲染。',
                        SpecialCategoryPage::FEATURE_CPD_RECORDS => '显示在 CPD 记录功能模块上方；直接粘贴或编写 HTML，保存后前台原样渲染。',
                        default => '显示在课程表格上方；直接粘贴或编写 HTML，保存后前台原样渲染。',
                    })
                    ->columnSpanFull()
                    ->extraInputAttributes([
                        'class' => 'font-mono text-sm',
                        'spellcheck' => 'false',
                    ]),
                Forms\Components\TextInput::make('certificate_title')
                    ->label('证书查询标题')
                    ->default('证书查询')
                    ->maxLength(255)
                    ->visible(fn (Get $get): bool => $get('feature_type') === SpecialCategoryPage::FEATURE_CERTIFICATE_LOOKUP)
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('certificate_summary')
                    ->label('证书查询摘要')
                    ->rows(3)
                    ->helperText('显示在证书查询表单上方，说明查询方式。')
                    ->visible(fn (Get $get): bool => $get('feature_type') === SpecialCategoryPage::FEATURE_CERTIFICATE_LOOKUP)
                    ->columnSpanFull(),
                Forms\Components\CheckboxList::make('course_category_ids')
                    ->label('展示的课程分类')
                    ->options(Course::CATEGORY_OPTIONS)
                    ->default(Course::categoryIds())
                    ->columns(2)
                    ->helperText('默认展示全部课程分类；可按需勾选子集。')
                    ->visible(fn (Get $get): bool => $get('feature_type') === SpecialCategoryPage::FEATURE_COURSE_LIST)
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('body_html_bottom')
                    ->label('正文（HTML 源码）')
                    ->rows(16)
                    ->helperText(fn (Get $get): string => match ($get('feature_type')) {
                        SpecialCategoryPage::FEATURE_CERTIFICATE_LOOKUP => '显示在证书查询模块下方；直接粘贴或编写 HTML，保存后前台原样渲染。',
                        SpecialCategoryPage::FEATURE_VIDEO_HUB => '显示在视频汇总模块下方；直接粘贴或编写 HTML，保存后前台原样渲染。',
                        SpecialCategoryPage::FEATURE_CPD_RECORDS => '显示在 CPD 记录功能模块下方；直接粘贴或编写 HTML，保存后前台原样渲染。',
                        default => '显示在课程表格下方；直接粘贴或编写 HTML，保存后前台原样渲染。',
                    })
                    ->columnSpanFull()
                    ->extraInputAttributes([
                        'class' => 'font-mono text-sm',
                        'spellcheck' => 'false',
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('category.name')
                    ->label('栏目')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('category.slug')
                    ->label('别名')
                    ->copyable(),
                Tables\Columns\TextColumn::make('feature_type')
                    ->label('功能类型')
                    ->formatStateUsing(fn (?string $state): string => SpecialCategoryPage::FEATURE_TYPE_OPTIONS[$state] ?? (string) $state),
                Tables\Columns\TextColumn::make('feature_detail')
                    ->label('功能说明')
                    ->getStateUsing(function (SpecialCategoryPage $record): string {
                        if ($record->feature_type === SpecialCategoryPage::FEATURE_CERTIFICATE_LOOKUP) {
                            return $record->certificateTitleForFrontend();
                        }

                        if ($record->feature_type === SpecialCategoryPage::FEATURE_VIDEO_HUB) {
                            return 'IPA播报 + IPA活动回顾';
                        }

                        if ($record->feature_type === SpecialCategoryPage::FEATURE_CPD_RECORDS) {
                            return '查询学分证明（日期筛选待开发）';
                        }

                        $ids = $record->course_category_ids;

                        if (! is_array($ids) || $ids === []) {
                            return '全部课程分类';
                        }

                        $labels = collect($ids)
                            ->map(fn (mixed $id): string => Course::CATEGORY_OPTIONS[(int) $id] ?? (string) $id)
                            ->filter()
                            ->values()
                            ->all();

                        return $labels !== [] ? implode('、', $labels) : '全部课程分类';
                    })
                    ->wrap(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('更新时间')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('updated_at', 'desc')
            ->actions([
                Actions\EditAction::make()->label('编辑'),
                Actions\DeleteAction::make()
                    ->label('删除')
                    ->after(function (SpecialCategoryPage $record): void {
                        Category::query()
                            ->whereKey($record->category_id)
                            ->update(['list_template' => CategoryListTemplateRegistry::TEMPLATE_SIMPLE]);
                    }),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make()
                        ->label('批量删除')
                        ->after(function ($records): void {
                            $categoryIds = collect($records)->pluck('category_id')->filter()->all();

                            if ($categoryIds !== []) {
                                Category::query()
                                    ->whereIn('id', $categoryIds)
                                    ->update(['list_template' => CategoryListTemplateRegistry::TEMPLATE_SIMPLE]);
                            }
                        }),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSpecialCategoryPages::route('/'),
            'create' => Pages\CreateSpecialCategoryPage::route('/create'),
            'edit' => Pages\EditSpecialCategoryPage::route('/{record}/edit'),
        ];
    }
}
