<?php

namespace App\Filament\Resources;

use App\Models\User;
use App\Filament\Resources\UserResource\Pages;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Support\Icons\Heroicon;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationLabel = '用户管理';

    protected static \BackedEnum|string|null $navigationIcon = Heroicon::Users;
    
    protected static ?int $navigationSort = 61;

    protected static string|\UnitEnum|null $navigationGroup = '权限管理';

    protected static ?string $modelLabel = '用户';

    protected static ?string $pluralModelLabel = '用户';
    public static function form(\Filament\Schemas\Schema $schema): \Filament\Schemas\Schema
    {
        return $schema
            ->components([
                Forms\Components\TextInput::make('name')
                    ->label('姓名')
                    ->required(),
                Forms\Components\TextInput::make('email')
                    ->label('邮箱')
                    ->email()
                    ->required(),
                Forms\Components\TextInput::make('password')
                    ->label('密码')
                    ->password()
                    ->dehydrated(fn ($state) => filled($state))
                    ->required(fn ($context) => $context === 'create'),
                Forms\Components\CheckboxList::make('roles')
                    ->label('角色')
                    ->relationship('roles', 'display_name')
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('姓名'),
                Tables\Columns\TextColumn::make('email')
                    ->label('邮箱'),
                Tables\Columns\TextColumn::make('roles.display_name')
                    ->label('角色')
                    ->badge()
                    ->separator(','),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('创建时间')
                    ->dateTime(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('roles')
                    ->label('角色')
                    ->relationship('roles', 'display_name'),
            ])
            ->headerActions([
                Actions\CreateAction::make(),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}