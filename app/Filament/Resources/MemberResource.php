<?php

namespace App\Filament\Resources;

use App\Models\Member;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;

class MemberResource extends Resource
{
    protected static ?string $model = Member::class;

    protected static \BackedEnum|string|null $navigationIcon = Heroicon::Users;

    protected static ?string $navigationLabel = '会员管理';

    protected static ?int $navigationSort = 13;

    protected static string|\UnitEnum|null $navigationGroup = '内容管理';

    protected static ?string $modelLabel = '会员';

    protected static ?string $pluralModelLabel = '会员';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\TextInput::make('name')
                    ->label('姓名')
                    ->required(),
                Forms\Components\TextInput::make('title')
                    ->label('职务')
                    ->required(),
                Forms\Components\FileUpload::make('avatar')
                    ->label('头像')
                    ->image()
                    ->directory('members'),
                Forms\Components\Textarea::make('bio')
                    ->label('简介')
                    ->required(),
                Forms\Components\Select::make('type')
                    ->label('类型')
                    ->options([
                        'leader' => '领导层',
                        'member' => '会员',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('sort_order')
                    ->label('排序')
                    ->default(0),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('姓名'),
                Tables\Columns\TextColumn::make('title')
                    ->label('职务'),
                Tables\Columns\ImageColumn::make('avatar')
                    ->label('头像'),
                Tables\Columns\TextColumn::make('type')
                    ->label('类型')
                    ->formatStateUsing(function ($state) {
                        $types = [
                            'leader' => '领导层',
                            'member' => '会员',
                        ];
                        return $types[$state] ?? $state;
                    }),
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('排序'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('创建时间')
                    ->dateTime(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('类型')
                    ->options([
                        'leader' => '领导层',
                        'member' => '会员',
                    ]),
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
            'index' => \App\Filament\Resources\MemberResource\Pages\ListMembers::route('/'),
            'create' => \App\Filament\Resources\MemberResource\Pages\CreateMember::route('/create'),
            'edit' => \App\Filament\Resources\MemberResource\Pages\EditMember::route('/{record}/edit'),
        ];
    }
}