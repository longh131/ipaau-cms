<?php

namespace App\Filament\Resources;

use App\Models\Subscriber;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SubscriberResource extends Resource
{
    protected static ?string $model = Subscriber::class;

    protected static \BackedEnum|string|null $navigationIcon = Heroicon::Envelope;

    protected static ?string $navigationLabel = '订阅管理';

    protected static ?string $modelLabel = '订阅';

    protected static ?string $pluralModelLabel = '订阅';

    protected static ?int $navigationSort = 51;

    protected static string|\UnitEnum|null $navigationGroup = '系统';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('姓名')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label('手机号')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('邮箱')
                    ->searchable(),
                Tables\Columns\TextColumn::make('company')
                    ->label('公司')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('job_title')
                    ->label('现任职务')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('education')
                    ->label('第一高等学历')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('subscribed_at')
                    ->label('订阅时间')
                    ->dateTime(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('创建时间')
                    ->dateTime(),
            ])
            ->filters([
                Tables\Filters\Filter::make('subscribed_at')
                    ->label('订阅时间')
                    ->form([
                        Forms\Components\DatePicker::make('subscribed_from')
                            ->label('开始日期'),
                        Forms\Components\DatePicker::make('subscribed_until')
                            ->label('结束日期'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['subscribed_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('subscribed_at', '>=', $date),
                            )
                            ->when(
                                $data['subscribed_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('subscribed_at', '<=', $date),
                            );
                    }),
            ])
            ->actions([
            ])
            ->bulkActions([
                Actions\ExportBulkAction::make()
                    ->label('导出'),
            ])
            ->defaultSort('subscribed_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\SubscriberResource\Pages\ListSubscribers::route('/'),
        ];
    }
}