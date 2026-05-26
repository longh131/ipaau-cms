<?php

namespace App\Filament\Resources;

use App\Models\ActivityLog;
use App\Filament\Resources\ActivityLogResource\Pages;
use Filament\Actions;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ActivityLogResource extends Resource
{
    protected static ?string $model = ActivityLog::class;

    protected static ?string $navigationLabel = '操作日志';

    protected static ?string $modelLabel = '日志';

    protected static ?string $pluralModelLabel = '操作日志';

    

    public static function form(\Filament\Schemas\Schema $schema): \Filament\Schemas\Schema
    {
        return $schema
            ->components([
                Forms\Components\TextInput::make('action')
                    ->label('操作'),
                Forms\Components\TextInput::make('target_type')
                    ->label('目标类型'),
                Forms\Components\TextInput::make('target_id')
                    ->label('目标ID'),
                Forms\Components\Textarea::make('description')
                    ->label('描述'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('用户'),
                Tables\Columns\TextColumn::make('action')
                    ->label('操作'),
                Tables\Columns\TextColumn::make('target_type')
                    ->label('目标类型'),
                Tables\Columns\TextColumn::make('description')
                    ->label('描述'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('时间')
                    ->dateTime(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('user_id')
                    ->label('用户')
                    ->relationship('user', 'name'),
            ])
            ->actions([
                Actions\ViewAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListActivityLogs::route('/'),
            'view' => Pages\ViewActivityLog::route('/{record}'),
        ];
    }
}