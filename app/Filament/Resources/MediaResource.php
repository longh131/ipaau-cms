<?php

namespace App\Filament\Resources;

use App\Models\Media;
use App\Filament\Resources\MediaResource\Pages;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Support\Icons\Heroicon;

class MediaResource extends Resource
{
    protected static ?string $model = Media::class;

    protected static ?string $navigationLabel = '媒体库';

    protected static \BackedEnum|string|null $navigationIcon = Heroicon::Photo;
    
    protected static ?int $navigationSort = 50;

    protected static string|\UnitEnum|null $navigationGroup = '系统';

    protected static ?string $modelLabel = '媒体文件';

    protected static ?string $pluralModelLabel = '媒体文件';

    public static function form(\Filament\Schemas\Schema $schema): \Filament\Schemas\Schema
    {
        return $schema
            ->components([
                Forms\Components\FileUpload::make('file_path')
                    ->label('文件')
                    ->required()
                    ->columnSpanFull()
                    ->disk('public')
                    ->directory('media')
                    ->storeFileNamesIn('name'),
                Forms\Components\TextInput::make('alt_text')
                    ->label('Alt 文本'),
                Forms\Components\Textarea::make('description')
                    ->label('描述'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('file_path')
                    ->label('预览')
                    ->size(40)
                    ->disk('public'),
                Tables\Columns\TextColumn::make('name')
                    ->label('文件名'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('上传时间')
                    ->dateTime(),
            ])
            ->actions([
                Actions\EditAction::make(),
                Actions\DeleteAction::make(),
            ])
            ->headerActions([
                Actions\CreateAction::make(),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMedia::route('/'),
            'create' => Pages\CreateMedia::route('/create'),
            'edit' => Pages\EditMedia::route('/{record}/edit'),
        ];
    }
}
