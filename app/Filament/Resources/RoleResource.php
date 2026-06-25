<?php

namespace App\Filament\Resources;

use App\Models\Role;
use App\Filament\Resources\RoleResource\Pages;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Support\Icons\Heroicon;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;

    protected static ?string $navigationLabel = '角色管理';

    protected static \BackedEnum|string|null $navigationIcon = Heroicon::ShieldCheck;

    protected static ?int $navigationSort = 60;

    protected static string|\UnitEnum|null $navigationGroup = '权限管理';

    protected static ?string $modelLabel = '角色';

    protected static ?string $pluralModelLabel = '角色';

    private const PRESETS = [
        'super_admin' => ['display_name' => '超级管理员', 'description' => '拥有全部权限，可管理角色与用户'],
        'admin' => ['display_name' => '管理员', 'description' => '管理内容与系统设置，不含角色管理'],
        'reviewer' => ['display_name' => '审核员', 'description' => '审核并发布内容'],
        'editor' => ['display_name' => '编辑员', 'description' => '创建与编辑内容，不可发布'],
    ];

    public static function form(\Filament\Schemas\Schema $schema): \Filament\Schemas\Schema
    {
        return $schema
            ->components([
                Forms\Components\Select::make('name')
                    ->label('角色类型')
                    ->options(collect(self::PRESETS)->mapWithKeys(fn ($v, $k) => [$k => $v['display_name']])->all())
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->live()
                    ->afterStateUpdated(function (?string $state, Set $set) {
                        if ($state && isset(self::PRESETS[$state])) {
                            $set('display_name', self::PRESETS[$state]['display_name']);
                            $set('description', self::PRESETS[$state]['description']);
                        }
                    })
                    ->helperText('首次部署可运行 php artisan db:seed --class=RolePermissionSeeder 初始化四种角色'),
                Forms\Components\TextInput::make('display_name')
                    ->label('显示名称')
                    ->required(),
                Forms\Components\Textarea::make('description')
                    ->label('描述')
                    ->rows(2),
                Forms\Components\CheckboxList::make('permissions')
                    ->label('权限')
                    ->relationship('permissions', 'display_name')
                    ->columns(2)
                    ->searchable()
                    ->bulkToggleable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('display_name')
                    ->label('显示名称'),
                Tables\Columns\TextColumn::make('name')
                    ->label('内部标识')
                    ->badge(),
                Tables\Columns\TextColumn::make('permissions.display_name')
                    ->label('权限')
                    ->badge()
                    ->limitList(3),
                Tables\Columns\TextColumn::make('description')
                    ->label('描述')
                    ->limit(40)
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('创建时间')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->headerActions([
                Actions\CreateAction::make(),
            ])
            ->actions([
                Actions\EditAction::make(),
                Actions\DeleteAction::make()
                    ->hidden(fn (Role $record) => array_key_exists($record->name, self::PRESETS)),
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
            'index' => Pages\ListRoles::route('/'),
            'create' => Pages\CreateRole::route('/create'),
            'edit' => Pages\EditRole::route('/{record}/edit'),
        ];
    }
}
