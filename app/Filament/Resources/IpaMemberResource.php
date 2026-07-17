<?php

namespace App\Filament\Resources;

use App\Filament\Resources\IpaMemberResource\Pages;
use App\Filament\Resources\IpaMemberResource\Schemas\IpaMemberFormSchema;
use App\Models\IpaMember;
use Filament\Actions;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;

class IpaMemberResource extends Resource
{
    protected static ?string $model = IpaMember::class;

    protected static \BackedEnum|string|null $navigationIcon = Heroicon::Identification;

    protected static ?string $navigationLabel = '持证会员';

    protected static ?int $navigationSort = 1;

    protected static string|\UnitEnum|null $navigationGroup = '会员管理';

    protected static ?string $modelLabel = '持证会员';

    protected static ?string $pluralModelLabel = '持证会员';

    public static function form(Schema $schema): Schema
    {
        return IpaMemberFormSchema::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('member_number')
                    ->label('持证会员编号')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('full_name')
                    ->label('姓名')
                    ->searchable()
                    ->formatStateUsing(fn (IpaMember $record): string => $record->display_name),
                Tables\Columns\TextColumn::make('mobile_phone')
                    ->label('手机号')
                    ->searchable(),
                Tables\Columns\TextColumn::make('member_level_short')
                    ->label('级别简称')
                    ->searchable(),
                Tables\Columns\TextColumn::make('membership_status')
                    ->label('资格状态')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('region')
                    ->label('所属区')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('joined_at')
                    ->label('加入日期')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('更新时间')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('member_number')
            ->filters([
                Tables\Filters\SelectFilter::make('member_level_short')
                    ->label('级别简称')
                    ->options(fn (): array => IpaMember::query()
                        ->whereNotNull('member_level_short')
                        ->distinct()
                        ->orderBy('member_level_short')
                        ->pluck('member_level_short', 'member_level_short')
                        ->all()),
                Tables\Filters\SelectFilter::make('membership_status')
                    ->label('资格状态')
                    ->options(fn (): array => IpaMember::query()
                        ->whereNotNull('membership_status')
                        ->distinct()
                        ->orderBy('membership_status')
                        ->pluck('membership_status', 'membership_status')
                        ->all()),
            ])
            ->actions([
                Actions\EditAction::make()->label('编辑'),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make()->label('批量删除'),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListIpaMembers::route('/'),
            'create' => Pages\CreateIpaMember::route('/create'),
            'edit' => Pages\EditIpaMember::route('/{record}/edit'),
            'statistics' => Pages\MemberStatistics::route('/statistics'),
        ];
    }
}
