<?php

namespace App\Filament\Resources;

use App\Models\MenuItem;
use App\Models\Menu;
use App\Models\Page;
use App\Models\Category;
use App\Models\Article;
use App\Filament\Resources\MenuItemResource\Pages;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class MenuItemResource extends Resource
{
    protected static ?string $model = MenuItem::class;

    protected static ?string $navigationLabel = '菜单项';

    protected static ?string $modelLabel = '菜单项';

    protected static ?string $pluralModelLabel = '菜单项';

    protected static ?string $navigationParentItem = MenuResource::class;

    public static function form(\Filament\Schemas\Schema $schema): \Filament\Schemas\Schema
    {
        return $schema
            ->components([
                Forms\Components\Select::make('menu_id')
                    ->label('所属菜单')
                    ->options(Menu::all()->pluck('name', 'id'))
                    ->required(),
                Forms\Components\Select::make('parent_id')
                    ->label('上级菜单')
                    ->options(function ($get) {
                        $menuId = $get('menu_id');
                        if (!$menuId) {
                            return MenuItem::whereNull('parent_id')->pluck('title', 'id');
                        }
                        return MenuItem::where('menu_id', $menuId)->whereNull('parent_id')->pluck('title', 'id');
                    })
                    ->nullable(),
                Forms\Components\TextInput::make('title')
                    ->label('菜单标题')
                    ->required(),
                Forms\Components\Select::make('link_type')
                    ->label('链接类型')
                    ->options([
                        'url' => '自定义URL',
                        'page' => '页面',
                        'category' => '栏目',
                        'article' => '文章',
                    ])
                    ->required()
                    ->default('url'),
                Forms\Components\Select::make('link_id')
                    ->label('链接目标')
                    ->options(function ($get) {
                        $linkType = $get('link_type');
                        switch ($linkType) {
                            case 'page':
                                return Page::where('is_active', true)->pluck('title', 'id');
                            case 'category':
                                return Category::where('is_active', true)->pluck('name', 'id');
                            case 'article':
                                return Article::where('is_active', true)->pluck('title', 'id');
                            default:
                                return [];
                        }
                    })
                    ->nullable(),
                Forms\Components\TextInput::make('url')
                    ->label('自定义URL')
                    ->url()
                    ->visible(fn ($get) => $get('link_type') === 'url'),
                Forms\Components\Select::make('target')
                    ->label('打开方式')
                    ->options([
                        '_self' => '当前窗口',
                        '_blank' => '新窗口',
                    ])
                    ->default('_self'),
                Forms\Components\TextInput::make('sort_order')
                    ->label('排序')
                    ->numeric()
                    ->default(0),
                Forms\Components\Toggle::make('is_active')
                    ->label('是否启用')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('menu.name')
                    ->label('所属菜单'),
                Tables\Columns\TextColumn::make('title')
                    ->label('菜单标题'),
                Tables\Columns\TextColumn::make('parent.title')
                    ->label('上级菜单'),
                Tables\Columns\TextColumn::make('link_type')
                    ->label('链接类型')
                    ->formatStateUsing(function ($state) {
                        $types = [
                            'url' => '自定义URL',
                            'page' => '页面',
                            'category' => '栏目',
                            'article' => '文章',
                        ];
                        return $types[$state] ?? $state;
                    }),
                Tables\Columns\TextColumn::make('url')
                    ->label('链接地址'),
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('排序'),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('是否启用')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('menu_id')
                    ->label('菜单')
                    ->options(Menu::all()->pluck('name', 'id')),
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
            'index' => Pages\ListMenuItems::route('/'),
            'create' => Pages\CreateMenuItem::route('/create'),
            'edit' => Pages\EditMenuItem::route('/{record}/edit'),
        ];
    }
}