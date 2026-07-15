<?php

namespace App\Filament\Resources\CategoryResource\RelationManagers;

use App\Filament\Concerns\GeneratesArticleSlug;
use App\Filament\Resources\ArticleResource;
use App\Filament\Resources\ArticleResource\Schemas\ArticleFormSchema;
use App\Models\Article;
use App\Models\Category;
use App\Support\ArticleExtraFields;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class ArticlesRelationManager extends RelationManager
{
    use GeneratesArticleSlug;

    protected static string $relationship = 'articles';

    protected static ?string $title = '栏目文章';

    protected static ?string $modelLabel = '文章';

    protected static ?string $pluralModelLabel = '文章';

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        return $ownerRecord instanceof Category && $ownerRecord->type === 'article';
    }

    public function form(Schema $schema): Schema
    {
        return ArticleFormSchema::configure($schema, $this->getOwnerRecord()->getKey());
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('标题')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('published_at')
                    ->label('发布时间')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('发布')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_sticky')
                    ->label('置顶')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_featured')
                    ->label('精选')
                    ->boolean(),
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('排序'),
            ])
            ->defaultSort('published_at', 'desc')
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('已发布'),
            ])
            ->headerActions([
                Actions\CreateAction::make()
                    ->label('新建文章')
                    ->mutateFormDataUsing(function (array $data): array {
                        return static::normalizeExtraFields($data, $this->getOwnerRecord());
                    }),
            ])
            ->actions([
                Actions\EditAction::make()
                    ->label('编辑')
                    ->mutateFormDataUsing(function (array $data): array {
                        return static::normalizeExtraFields($data, $this->getOwnerRecord());
                    }),
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

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected static function normalizeExtraFields(array $data, Category $category): array
    {
        $schema = ArticleExtraFields::normalizeSchema($category->article_extra_field_schema);
        $data['extra_fields'] = ArticleExtraFields::normalizeValuesForStorage($data['extra_fields'] ?? [], $schema);
        $data['category_id'] = $category->getKey();

        return $data;
    }
}
