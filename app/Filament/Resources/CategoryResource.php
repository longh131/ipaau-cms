<?php

namespace App\Filament\Resources;

use Illuminate\Support\Facades\Schema as SchemaFacade;
use App\Filament\Resources\ArticleResource;
use App\Filament\Resources\CategoryResource\RelationManagers\ArticlesRelationManager;
use App\Models\Category;
use App\Models\Setting;
use App\Support\ArticleExtraFields;
use App\Support\CategoryIntroduction;
use App\Support\CategoryListTemplate\CategoryListTemplateRegistry;
use App\Support\RichContent;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static \BackedEnum|string|null $navigationIcon = Heroicon::Folder;

    protected static ?string $navigationLabel = '栏目管理';
    
    protected static ?int $navigationSort = 4;

    protected static ?string $modelLabel = '栏目';

    protected static ?string $pluralModelLabel = '栏目';

    protected static function getEnabledTypeOptions(): array
    {
        $allTypes = Category::getTypeOptions();

        if (!SchemaFacade::hasTable('settings')) {
            return $allTypes;
        }

        $enabledTypes = Setting::get('enabled_content_types', ['article', 'page', 'link', 'member']);

        return array_filter($allTypes, fn ($key) => in_array($key, $enabledTypes), ARRAY_FILTER_USE_KEY);
    }

    protected static function buildTreeOptions($categories, $prefix = ''): array
    {
        $options = [];
        foreach ($categories as $category) {
            $options[$category->id] = $prefix . $category->name;
            if ($category->children && $category->children->count() > 0) {
                $options = $options + static::buildTreeOptions($category->children, $prefix . '→ ');
            }
        }
        return $options;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public static function normalizeFormData(array $data): array
    {
        $data['introduction'] = CategoryIntroduction::forForm($data['introduction'] ?? null);

        return $data;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public static function normalizeStorageData(array $data): array
    {
        $data['introduction'] = CategoryIntroduction::forStorage($data['introduction'] ?? null);

        return $data;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\TextInput::make('name')
                    ->label('名称')
                    ->required(),
                Forms\Components\TextInput::make('slug')
                    ->label('别名')
                    ->required()
                    ->unique(ignoreRecord: true),
                Forms\Components\Select::make('parent_id')
                    ->label('上级栏目')
                    ->options(function () {
                        $options = [0 => '无（作为顶级栏目）'];
                        $categories = Category::getSortedTree();
                        $options = $options + static::buildTreeOptions($categories);
                        return $options;
                    })
                    ->default(0),
                Forms\Components\Select::make('type')
                    ->label('类型')
                    ->options(static::getEnabledTypeOptions())
                    ->required()
                    ->live(),
                Forms\Components\Select::make('list_template')
                    ->label('列表模板')
                    ->options(CategoryListTemplateRegistry::OPTIONS)
                    ->default(CategoryListTemplateRegistry::TEMPLATE_SIMPLE)
                    ->helperText('仅对「文章」类型栏目生效，决定前台列表展示样式')
                    ->visible(fn (Get $get): bool => $get('type') === 'article')
                    ->columnSpanFull(),
                RichContent::configureFileAttachments(
                    Forms\Components\RichEditor::make('introduction')
                        ->label('栏目介绍')
                        ->json()
                        ->toolbarButtons(RichContent::pageToolbar())
                        ->helperText('显示在栏目标题下方；建议说明本栏目内容范围，如专业技术、数字咨询、会刊精选等')
                        ->columnSpanFull(),
                )
                    ->visible(fn (Get $get): bool => $get('type') === 'article')
                    ->columnSpanFull(),
                Forms\Components\Repeater::make('article_extra_field_schema')
                    ->label('文章扩展字段')
                    ->helperText('定义该栏目下文章的额外字段；前台按模板输出')
                    ->schema(ArticleExtraFields::categorySchemaRepeaterFields())
                    ->columns(2)
                    ->collapsible()
                    ->itemLabel(fn (array $state): string => filled($state['label'] ?? null)
                        ? (string) $state['label']
                        : ((string) ($state['key'] ?? '字段')))
                    ->visible(fn (Get $get): bool => $get('type') === 'article')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('sort_order')
                    ->label('排序')
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
                Tables\Columns\ViewColumn::make('name')
                    ->label('名称')
                    ->view('filament.resources.category.columns.tree-name'),
                Tables\Columns\TextColumn::make('slug')
                    ->label('别名'),
                Tables\Columns\TextColumn::make('type')
                    ->label('类型')
                    ->formatStateUsing(function ($state) {
                        return Category::getTypeOptions()[$state] ?? $state;
                    }),
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('排序'),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('启用')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('创建时间')
                    ->dateTime(),
            ])
            ->modifyQueryUsing(function (Builder $query) {
                $categories = Category::getSortedTree();
                $sortedIds = [];
                $collectIds = function ($items) use (&$collectIds, &$sortedIds) {
                    foreach ($items as $item) {
                        $sortedIds[] = $item->id;
                        if ($item->children && $item->children->count() > 0) {
                            $collectIds($item->children);
                        }
                    }
                };
                $collectIds($categories);
                if (!empty($sortedIds)) {
                    $safeIds = implode(',', array_map('intval', $sortedIds));

                    return $query->whereIn('id', $sortedIds)->orderByRaw('FIELD(id, ' . $safeIds . ')');
                }
                return $query;
            })
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('类型')
                    ->options(static::getEnabledTypeOptions()),
            ])
            ->actions([
                Actions\Action::make('articles')
                    ->label('文章')
                    ->icon(Heroicon::DocumentText)
                    ->url(fn (Category $record): string => ArticleResource::getUrl('index', [
                        'category_id' => $record->getKey(),
                    ]))
                    ->visible(fn (Category $record): bool => $record->type === 'article'),
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
            'index' => \App\Filament\Resources\CategoryResource\Pages\ListCategories::route('/'),
            'create' => \App\Filament\Resources\CategoryResource\Pages\CreateCategory::route('/create'),
            'edit' => \App\Filament\Resources\CategoryResource\Pages\EditCategory::route('/{record}/edit'),
        ];
    }

    public static function getRelations(): array
    {
        return [
            ArticlesRelationManager::class,
        ];
    }
}