<?php

namespace App\Filament\Resources;

use App\Models\MenuItem;
use App\Models\Menu;
use App\Models\Page;
use App\Models\Category;
use App\Models\Article;
use App\Support\MenuItemLink;
use App\Filament\Resources\MenuItemResource\Pages;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Components\Utilities\Get;
use Illuminate\Database\Eloquent\Builder;

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
                    ->options(function (Get $get) {
                        $menuId = $get('menu_id');
                        $query = MenuItem::query()->orderBy('sort_order');
                        if ($menuId) {
                            $query->where('menu_id', $menuId);
                        }

                        return $query->pluck('title', 'id')->prepend('无（顶级菜单）', '');
                    })
                    ->nullable(),
                Forms\Components\TextInput::make('title')
                    ->label('菜单标题')
                    ->required(),
                Forms\Components\Select::make('link_type')
                    ->label('链接类型')
                    ->options(MenuItemLink::typeOptions())
                    ->default(MenuItemLink::TYPE_URL)
                    ->live()
                    ->dehydrated(true),
                Forms\Components\Select::make('link_id')
                    ->label('链接目标')
                    ->options(function (Get $get) {
                        return match ($get('link_type')) {
                            MenuItemLink::TYPE_PAGE => Page::where('is_active', true)->pluck('title', 'id'),
                            MenuItemLink::TYPE_CATEGORY => Category::where('is_active', true)->pluck('name', 'id'),
                            MenuItemLink::TYPE_ARTICLE => Article::where('is_active', true)->pluck('title', 'id'),
                            default => [],
                        };
                    })
                    ->searchable()
                    ->visible(fn (Get $get) => $get('link_type') && $get('link_type') !== MenuItemLink::TYPE_URL)
                    ->required(fn (Get $get) => $get('link_type') && $get('link_type') !== MenuItemLink::TYPE_URL)
                    ->dehydrated(true),
                Forms\Components\TextInput::make('url')
                    ->label('自定义 URL')
                    ->url()
                    ->visible(fn (Get $get) => ($get('link_type') ?: MenuItemLink::TYPE_URL) === MenuItemLink::TYPE_URL)
                    ->required(fn (Get $get) => ($get('link_type') ?: MenuItemLink::TYPE_URL) === MenuItemLink::TYPE_URL),
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
            ->paginated([50])
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('菜单标题')
                    ->formatStateUsing(function ($state, $record) {
                        $depth = static::getDepth($record);
                        $hasChildren = MenuItem::where('parent_id', $record->id)->exists();

                        return view('filament.resources.menu-item.columns.tree-title', [
                            'title' => $state,
                            'depth' => $depth,
                            'hasChildren' => $hasChildren,
                            'recordId' => $record->id,
                            'isActive' => $record->is_active,
                            'parentId' => $record->parent_id,
                        ]);
                    })
                    ->html(),
                Tables\Columns\TextColumn::make('url')
                    ->label('链接')
                    ->formatStateUsing(fn ($state, MenuItem $record) => MenuItemLink::resolveUrl($record))
                    ->toggleable(),
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('排序')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('启用')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('menu_id')
                    ->label('菜单')
                    ->options(Menu::all()->pluck('name', 'id')),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('仅显示启用的'),
            ])
            ->headerActions([
                Actions\CreateAction::make(),
                Actions\Action::make('importCategories')
                    ->label('批量导入栏目')
                    ->icon(Heroicon::Plus)
                    ->form([
                        Forms\Components\Select::make('menu_id')
                            ->label('选择菜单')
                            ->options(Menu::all()->pluck('name', 'id'))
                            ->required()
                            ->default(fn () => Menu::first()?->id),
                        Forms\Components\CheckboxList::make('category_ids')
                            ->label('选择栏目（可多选）')
                            ->options(function () {
                                $categories = Category::where('parent_id', 0)->get();
                                $options = [];
                                foreach ($categories as $category) {
                                    $options[$category->id] = $category->name;
                                    foreach (Category::where('parent_id', $category->id)->orderBy('sort_order')->get() as $child) {
                                        $options[$child->id] = '└─ '.$child->name;
                                        foreach (Category::where('parent_id', $child->id)->orderBy('sort_order')->get() as $grandchild) {
                                            $options[$grandchild->id] = '  └─ '.$grandchild->name;
                                        }
                                    }
                                }

                                return $options;
                            })
                            ->columns(3)
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($state, Set $set) {
                                $set('all_selected', count($state) === Category::count());
                            }),
                        Forms\Components\Checkbox::make('all_selected')
                            ->label('全选所有栏目')
                            ->live()
                            ->afterStateUpdated(function ($state, Set $set) {
                                $set('category_ids', $state ? Category::pluck('id')->all() : []);
                            }),
                        Forms\Components\Checkbox::make('include_descendants')
                            ->label('包含选中栏目的所有子栏目')
                            ->default(true),
                    ])
                    ->action(function (array $data) {
                        $menuId = $data['menu_id'];
                        $categoryIds = $data['category_ids'] ?? [];
                        $includeDescendants = $data['include_descendants'] ?? true;

                        if (empty($categoryIds)) {
                            Notification::make()
                                ->title('请选择栏目')
                                ->danger()
                                ->send();

                            return;
                        }

                        $allCategoryIds = $categoryIds;
                        if ($includeDescendants) {
                            foreach ($categoryIds as $catId) {
                                $allCategoryIds = array_merge(
                                    $allCategoryIds,
                                    static::collectCategoryDescendantIds((int) $catId)
                                );
                            }
                        }

                        $allCategoryIds = array_values(array_unique($allCategoryIds));
                        $categories = Category::whereIn('id', $allCategoryIds)->get()->keyBy('id');
                        $sorted = static::sortCategoriesByDepth($categories);

                        $categoryToMenuItemMap = [];
                        $count = 0;

                        foreach ($sorted as $category) {
                            $parentMenuItemId = $category->parent_id
                                ? ($categoryToMenuItemMap[$category->parent_id] ?? null)
                                : null;

                            $menuItem = MenuItem::create([
                                'menu_id' => $menuId,
                                'parent_id' => $parentMenuItemId,
                                'title' => $category->name,
                                'route' => MenuItemLink::ROUTE_MAP[MenuItemLink::TYPE_CATEGORY],
                                'route_params' => json_encode(['slug' => $category->slug], JSON_UNESCAPED_UNICODE),
                                'sort_order' => $category->sort_order ?? 0,
                                'is_active' => (bool) $category->is_active,
                            ]);

                            $categoryToMenuItemMap[$category->id] = $menuItem->id;
                            $count++;
                        }

                        Notification::make()
                            ->title('导入成功')
                            ->body("已成功导入 {$count} 个栏目到菜单")
                            ->success()
                            ->send();
                    }),
            ])
            ->actions([
                Actions\EditAction::make(),
                Actions\DeleteAction::make(),
                Actions\Action::make('moveUp')
                    ->label('上移')
                    ->icon(Heroicon::ChevronUp)
                    ->action(function ($record) {
                        $siblings = MenuItem::where('menu_id', $record->menu_id)
                            ->where('parent_id', $record->parent_id)
                            ->where('sort_order', '<', $record->sort_order)
                            ->orderByDesc('sort_order')
                            ->first();

                        if ($siblings) {
                            [$record->sort_order, $siblings->sort_order] = [$siblings->sort_order, $record->sort_order];
                            $record->save();
                            $siblings->save();
                        }
                    }),
                Actions\Action::make('moveDown')
                    ->label('下移')
                    ->icon(Heroicon::ChevronDown)
                    ->action(function ($record) {
                        $siblings = MenuItem::where('menu_id', $record->menu_id)
                            ->where('parent_id', $record->parent_id)
                            ->where('sort_order', '>', $record->sort_order)
                            ->orderBy('sort_order')
                            ->first();

                        if ($siblings) {
                            [$record->sort_order, $siblings->sort_order] = [$siblings->sort_order, $record->sort_order];
                            $record->save();
                            $siblings->save();
                        }
                    }),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->modifyQueryUsing(function (Builder $query) {
                $query->orderBy('parent_id')->orderBy('sort_order');
            });
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMenuItems::route('/'),
            'create' => Pages\CreateMenuItem::route('/create'),
            'edit' => Pages\EditMenuItem::route('/{record}/edit'),
        ];
    }

    private static function getDepth(MenuItem $record): int
    {
        $depth = 0;
        $parentId = $record->parent_id;
        while ($parentId) {
            $parent = MenuItem::find($parentId);
            if (! $parent) {
                break;
            }
            $depth++;
            $parentId = $parent->parent_id;
        }

        return $depth;
    }

    private static function collectCategoryDescendantIds(int $categoryId): array
    {
        $ids = [];
        foreach (Category::where('parent_id', $categoryId)->pluck('id') as $childId) {
            $ids[] = $childId;
            $ids = array_merge($ids, static::collectCategoryDescendantIds((int) $childId));
        }

        return $ids;
    }

    private static function sortCategoriesByDepth($categories)
    {
        $depth = function ($category) use ($categories, &$depth) {
            if (! $category->parent_id || ! $categories->has($category->parent_id)) {
                return 0;
            }

            return 1 + $depth($categories->get($category->parent_id));
        };

        return $categories->sortBy(fn ($category) => [$depth($category), $category->sort_order, $category->id])->values();
    }
}
