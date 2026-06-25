<?php

namespace App\Filament\Resources;

use App\Models\ActivityLog;
use App\Filament\Resources\ActivityLogResource\Pages;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Support\Icons\Heroicon;

class ActivityLogResource extends Resource
{
    protected static ?string $model = ActivityLog::class;

    protected static ?string $navigationLabel = '操作日志';

    protected static \BackedEnum|string|null $navigationIcon = Heroicon::Clock;

    protected static ?int $navigationSort = 62;

    protected static string|\UnitEnum|null $navigationGroup = '权限管理';

    protected static ?string $modelLabel = '日志';

    protected static ?string $pluralModelLabel = '操作日志';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('用户')
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('event')
                    ->label('事件')
                    ->badge(),
                Tables\Columns\TextColumn::make('model_type')
                    ->label('模型')
                    ->formatStateUsing(fn (?string $state) => $state ? class_basename($state) : '—'),
                Tables\Columns\TextColumn::make('model_id')
                    ->label('记录 ID'),
                Tables\Columns\TextColumn::make('ip_address')
                    ->label('IP')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('时间')
                    ->dateTime(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('user_id')
                    ->label('用户')
                    ->relationship('user', 'name'),
                Tables\Filters\SelectFilter::make('event')
                    ->label('事件')
                    ->options(fn () => ActivityLog::query()->distinct()->pluck('event', 'event')->filter()->all()),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListActivityLogs::route('/'),
        ];
    }
}
