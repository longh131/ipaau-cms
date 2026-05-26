<?php

namespace App\Filament\Resources;

use App\Models\Menu;
use App\Filament\Resources\MenuResource\Pages;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class MenuResource extends Resource
{
    protected static ?string $model = Menu::class;

    protected static ?string $navigationLabel = '菜单管理';

    protected static ?string $modelLabel = '菜单';

    protected static ?string $pluralModelLabel = '菜单';

    public static function form(\Filament\Schemas\Schema $schema): \Filament\Schemas\Schema
    {
        return $schema
            ->components([
                Forms\Components\TextInput::make('name')
                    ->label('菜单名称')
                    ->required(),
                Forms\Components\TextInput::make('slug')
                    ->label('标识')
                    ->required(),
                Forms\Components\Textarea::make('description')
                    ->label('描述'),
                Forms\Components\Toggle::make('is_active')
                    ->label('是否启用')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('菜单名称'),
                Tables\Columns\TextColumn::make('slug')
                    ->label('标识'),
                Tables\Columns\TextColumn::make('description')
                    ->label('描述'),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('是否启用')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('创建时间')
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
            'index' => Pages\ListMenus::route('/'),
            'create' => Pages\CreateMenu::route('/create'),
            'edit' => Pages\EditMenu::route('/{record}/edit'),
        ];
    }
}