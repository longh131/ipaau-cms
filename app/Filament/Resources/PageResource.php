<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PageResource\Forms\BasicContentPageForm;
use App\Filament\Resources\PageResource\Forms\DefaultPageForm;
use App\Filament\Resources\PageResource\Forms\GeneralSecondaryPageForm;
use App\Filament\Resources\PageResource\Forms\GovernancePageForm;
use App\Models\Category;
use App\Models\Page;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;

class PageResource extends Resource
{
    protected static ?string $model = Page::class;

    protected static \BackedEnum|string|null $navigationIcon = Heroicon::DocumentText;

    protected static ?string $navigationLabel = '页面管理';

    protected static ?int $navigationSort = 12;

    protected static string|\UnitEnum|null $navigationGroup = '内容管理';

    protected static ?string $modelLabel = '页面';

    protected static ?string $pluralModelLabel = '页面';

    /**
     * @return array<int, string>
     */
    public static function pageCategoryOptions(?int $pageId = null): array
    {
        return Category::pageSelectOptions($pageId);
    }

    public static function form(Schema $schema): Schema
    {
        $defaultTemplateFields = collect(DefaultPageForm::schema())
            ->map(fn ($component) => $component->visible(
                fn (Get $get): bool => ($get('template') ?? Page::TEMPLATE_DEFAULT) === Page::TEMPLATE_DEFAULT,
            ))
            ->all();

        $basicContentTemplateFields = collect(BasicContentPageForm::schema())
            ->map(fn ($component) => $component->visible(
                fn (Get $get): bool => ($get('template') ?? Page::TEMPLATE_DEFAULT) === Page::TEMPLATE_BASIC_CONTENT,
            ))
            ->all();

        $governanceTemplateFields = collect(GovernancePageForm::schema())
            ->map(fn ($component) => $component->visible(
                fn (Get $get): bool => ($get('template') ?? Page::TEMPLATE_DEFAULT) === Page::TEMPLATE_GOVERNANCE,
            ))
            ->all();

        $generalSecondaryTemplateFields = collect(GeneralSecondaryPageForm::schema())
            ->map(fn ($component) => $component->visible(
                fn (Get $get): bool => ($get('template') ?? Page::TEMPLATE_DEFAULT) === Page::TEMPLATE_GENERAL_SECONDARY,
            ))
            ->all();

        return $schema
            ->components([
                Forms\Components\Select::make('category_id')
                    ->label('所属单页栏目')
                    ->options(function (): array {
                        $livewire = \Livewire\Livewire::current();
                        $pageId = null;

                        if ($livewire && method_exists($livewire, 'getRecord')) {
                            $pageId = $livewire->getRecord()?->getKey();
                        }

                        return static::pageCategoryOptions(is_numeric($pageId) ? (int) $pageId : null);
                    })
                    ->searchable()
                    ->required()
                    ->live()
                    ->helperText('仅显示栏目类型为「单页」且尚未绑定其他页面的栏目')
                    ->afterStateUpdated(function (?string $state, Set $set, Get $get): void {
                        if (blank($state)) {
                            return;
                        }

                        $category = Category::query()->find($state);

                        if (! $category) {
                            return;
                        }

                        $set('slug', $category->slug);

                        if (blank($get('title'))) {
                            $set('title', $category->name);
                        }
                    }),
                Forms\Components\TextInput::make('title')
                    ->label('页面标题')
                    ->required()
                    ->maxLength(255)
                    ->helperText('用于 SEO 及后台识别；默认正文页的前台 H1 请在正文区块中添加，基本正文页请在下方「标题」字段填写'),
                Forms\Components\TextInput::make('slug')
                    ->label('URL 标识')
                    ->disabled()
                    ->dehydrated()
                    ->helperText('自动使用所绑定栏目的别名，前台地址：/category/{别名}'),
                Forms\Components\Select::make('template')
                    ->label('页面模板')
                    ->options(Page::TEMPLATE_OPTIONS)
                    ->default(Page::TEMPLATE_DEFAULT)
                    ->required()
                    ->live(),
                ...$defaultTemplateFields,
                ...$basicContentTemplateFields,
                ...$governanceTemplateFields,
                ...$generalSecondaryTemplateFields,
                Forms\Components\TextInput::make('meta_title')
                    ->label('SEO 标题')
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('meta_description')
                    ->label('SEO 描述')
                    ->rows(3)
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('sort_order')
                    ->label('排序')
                    ->numeric()
                    ->default(0),
                Forms\Components\Toggle::make('is_active')
                    ->label('是否启用')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('标题')
                    ->searchable(),
                Tables\Columns\TextColumn::make('category.name')
                    ->label('所属栏目')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('slug')
                    ->label('URL 标识')
                    ->searchable(),
                Tables\Columns\TextColumn::make('template')
                    ->label('模板')
                    ->formatStateUsing(fn (?string $state): string => Page::TEMPLATE_OPTIONS[$state] ?? $state ?? ''),
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('排序')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('是否启用')
                    ->boolean(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('更新时间')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('sort_order')
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('是否启用'),
            ])
            ->actions([
                Actions\EditAction::make()
                    ->label('编辑'),
                Actions\DeleteAction::make()
                    ->label('删除'),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make()
                        ->label('批量删除'),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\PageResource\Pages\ListPages::route('/'),
            'create' => \App\Filament\Resources\PageResource\Pages\CreatePage::route('/create'),
            'edit' => \App\Filament\Resources\PageResource\Pages\EditPage::route('/{record}/edit'),
        ];
    }
}
