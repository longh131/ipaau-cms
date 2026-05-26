<?php

namespace App\Filament\Resources;

use App\Models\Media;
use App\Filament\Resources\MediaResource\Pages;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class MediaResource extends Resource
{
    protected static ?string $model = Media::class;

    protected static ?string $navigationLabel = '媒体库';

    protected static ?string $modelLabel = '媒体文件';

    protected static ?string $pluralModelLabel = '媒体文件';

    

    public static function form(\Filament\Schemas\Schema $schema): \Filament\Schemas\Schema
    {
        return $schema
            ->components([
                Forms\Components\FileUpload::make('path')
                    ->label('文件')
                    ->required()
                    ->columnSpanFull()
                    ->directory('media')
                    ->preserveFilenames(),
                Forms\Components\TextInput::make('name')
                    ->label('名称'),
                Forms\Components\TextInput::make('type')
                    ->label('类型')
                    ->disabled(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('path')
                    ->label('预览')
                    ->size(40),
                Tables\Columns\TextColumn::make('name')
                    ->label('文件名'),
                Tables\Columns\TextColumn::make('type')
                    ->label('类型'),
                Tables\Columns\TextColumn::make('formatted_size')
                    ->label('大小'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('上传时间')
                    ->dateTime(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('仅显示启用的'),
            ])
            ->actions([
                Actions\EditAction::make(),
                Actions\DeleteAction::make(),
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