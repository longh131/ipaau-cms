<?php

namespace App\Filament\Resources;

use App\Models\Page;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Resource;
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

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\TextInput::make('title')
                    ->label('标题')
                    ->required(),
                Forms\Components\TextInput::make('slug')
                    ->label('URL 标识')
                    ->required()
                    ->unique(ignoreRecord: true),
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
                Forms\Components\TextInput::make('meta_title')
                    ->label('SEO 标题')
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('meta_description')
                    ->label('SEO 描述')
                    ->rows(3)
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
                Tables\Columns\TextColumn::make('title')
                    ->label('标题'),
                Tables\Columns\TextColumn::make('slug')
                    ->label('URL 标识'),
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('排序'),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('是否启用')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('创建时间')
                    ->dateTime(),
            ])
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