<?php

namespace App\Filament\Resources;

use App\Models\Article;
use App\Models\Category;
use Filament\Actions;
use Filament\Forms;
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
    
    protected static ?int $navigationSort = 5;

    protected static ?string $modelLabel = '文章';

    protected static ?string $pluralModelLabel = '文章';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\TextInput::make('title')
                    ->label('标题')
                    ->required(),
                Forms\Components\TextInput::make('slug')
                    ->label('别名')
                    ->required()
                    ->unique(ignoreRecord: true),
                Forms\Components\Select::make('category_id')
                    ->label('所属栏目')
                    ->options(function () {
                        return Category::query()
                            ->orderBy('type')
                            ->orderBy('sort_order')
                            ->pluck('name', 'id');
                    })
                    ->searchable()
                    ->required(),
                Forms\Components\TextInput::make('redirect_url')
                    ->label('跳转链接')
                    ->placeholder('如果填写了链接，点击文章时将跳转到外部链接')
                    ->url(),
                Forms\Components\Textarea::make('summary')
                    ->label('文章摘要')
                    ->rows(3)
                    ->columnSpanFull(),
                Forms\Components\RichEditor::make('content')
                    ->label('内容')
                    ->required()
                    ->columnSpanFull()
                    ->toolbarButtons([
                        ['bold', 'italic', 'underline', 'strike'],
                        ['h2', 'h3', 'blockquote', 'codeBlock'],
                        ['bulletList', 'orderedList'],
                        ['link', 'attachFiles'],
                        ['undo', 'redo'],
                        ['source-ai'],
                    ]),
                Forms\Components\DateTimePicker::make('published_at')
                    ->label('发布时间')
                    ->columnSpan(1),
                Forms\Components\TextInput::make('sort_order')
                    ->label('排序')
                    ->default(0)
                    ->columnSpan(1),
                Forms\Components\Toggle::make('is_sticky')
                    ->label('置顶')
                    ->default(false)
                    ->columnSpan(1),
                Forms\Components\Toggle::make('is_featured')
                    ->label('是否精选')
                    ->default(false)
                    ->columnSpan(1),
                Forms\Components\KeyValue::make('extra_fields')
                    ->label('扩展字段')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('标题'),
                Tables\Columns\TextColumn::make('category.name')
                    ->label('所属栏目'),
                Tables\Columns\TextColumn::make('published_at')
                    ->label('发布时间')
                    ->dateTime(),
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
                    ->dateTime(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category_id')
                    ->label('栏目')
                    ->relationship('category', 'name'),
                Tables\Filters\Filter::make('published_at')
                    ->label('发布时间')
                    ->form([
                        Forms\Components\DatePicker::make('published_from')
                            ->label('开始日期'),
                        Forms\Components\DatePicker::make('published_until')
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
            'index' => \App\Filament\Resources\ArticleResource\Pages\ListArticles::route('/'),
            'create' => \App\Filament\Resources\ArticleResource\Pages\CreateArticle::route('/create'),
            'edit' => \App\Filament\Resources\ArticleResource\Pages\EditArticle::route('/{record}/edit'),
        ];
    }
}
