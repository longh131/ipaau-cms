<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ArticleResource\Pages;
use App\Filament\Resources\ArticleResource\Schemas\ArticleFormSchema;
use App\Models\Article;
use App\Models\Category;
use App\Support\ArticleExtraFields;
use App\Support\ArticleSlug;
use App\Support\MediaUrl;
use Filament\Actions;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ArticleResource extends Resource
{
    protected static ?string $model = Article::class;

    protected static \BackedEnum|string|null $navigationIcon = Heroicon::DocumentText;

    protected static ?string $navigationLabel = '文章管理';

    protected static ?int $navigationSort = 11;

    protected static string|\UnitEnum|null $navigationGroup = '内容管理';

    protected static ?string $modelLabel = '文章';

    protected static ?string $pluralModelLabel = '文章';

    public static function form(Schema $schema): Schema
    {
        return ArticleFormSchema::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('标题')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('category.name')
                    ->label('所属栏目')
                    ->sortable(),
                Tables\Columns\TextColumn::make('published_at')
                    ->label('发布时间')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('发布')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_featured')
                    ->label('精选')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_sticky')
                    ->label('置顶')
                    ->boolean(),
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('排序'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('创建时间')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('published_at', 'desc')
            ->persistFiltersInSession()
            ->filters([
                Tables\Filters\SelectFilter::make('category_id')
                    ->label('栏目')
                    ->relationship('category', 'name'),
                Tables\Filters\Filter::make('published_at')
                    ->label('发布时间')
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('published_from')
                            ->label('开始日期'),
                        \Filament\Forms\Components\DatePicker::make('published_until')
                            ->label('结束日期'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['published_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('published_at', '>=', $date),
                            )
                            ->when(
                                $data['published_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('published_at', '<=', $date),
                            );
                    }),
                Tables\Filters\TernaryFilter::make('is_featured')
                    ->label('精选'),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('已发布'),
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
            'index' => Pages\ListArticles::route('/'),
            'create' => Pages\CreateArticle::route('/create'),
            'edit' => Pages\EditArticle::route('/{record}/edit'),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public static function normalizeArticleData(array $data): array
    {
        $categoryId = $data['category_id'] ?? null;
        $category = $categoryId ? Category::query()->find($categoryId) : null;
        $schema = ArticleExtraFields::normalizeSchema($category?->article_extra_field_schema);
        $data['extra_fields'] = ArticleExtraFields::normalizeValuesForStorage($data['extra_fields'] ?? [], $schema);
        $data['cover_image'] = filled($data['cover_image'] ?? null)
            ? MediaUrl::normalizeStoredPath($data['cover_image'])
            : null;
        $data['author'] = filled($data['author'] ?? null) ? trim((string) $data['author']) : null;
        $data['source'] = filled($data['source'] ?? null) ? trim((string) $data['source']) : null;
        $data['view_count'] = max(0, (int) ($data['view_count'] ?? 0));

        if (blank($data['slug'] ?? null) && filled($data['title'] ?? null)) {
            $ignoreId = is_numeric($data['id'] ?? null) ? (int) $data['id'] : null;
            $data['slug'] = ArticleSlug::fromTitle((string) $data['title'], $ignoreId);
        }

        return $data;
    }
}
