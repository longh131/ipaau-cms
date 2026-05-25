<?php

namespace App\Filament\Resources;

use App\Models\PageComponent;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;

class PageComponentResource extends Resource
{
    protected static ?string $model = PageComponent::class;

    protected static \BackedEnum|string|null $navigationIcon = Heroicon::ViewColumns;

    protected static ?string $navigationLabel = '页面组件';

    protected static ?string $modelLabel = '页面组件';

    protected static ?string $pluralModelLabel = '页面组件';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\TextInput::make('page_slug')
                    ->label('页面标识')
                    ->default('home'),
                Forms\Components\TextInput::make('component_type')
                    ->label('组件类型')
                    ->required(),
                Forms\Components\TextInput::make('sort_order')
                    ->label('排序')
                    ->default(0),
                Forms\Components\KeyValue::make('data')
                    ->label('组件数据')
                    ->columnSpanFull(),
                Forms\Components\Toggle::make('is_active')
                    ->label('是否启用')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('page_slug')
                    ->label('页面标识'),
                Tables\Columns\TextColumn::make('component_type')
                    ->label('组件类型'),
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('排序'),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('启用状态')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('创建时间')
                    ->dateTime(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('component_type')
                    ->label('组件类型')
                    ->options(function () {
                        return PageComponent::distinct()
                            ->pluck('component_type', 'component_type');
                    }),
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
            'index' => \App\Filament\Resources\PageComponentResource\Pages\ListPageComponents::route('/'),
            'create' => \App\Filament\Resources\PageComponentResource\Pages\CreatePageComponent::route('/create'),
            'edit' => \App\Filament\Resources\PageComponentResource\Pages\EditPageComponent::route('/{record}/edit'),
        ];
    }
}