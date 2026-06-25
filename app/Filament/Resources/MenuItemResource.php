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
use Filament\Schemas\Components\Section;
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
                    ->required()
                    ->default(fn () => request()->integer('menu_id') ?: null),
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
                Section::make('下拉推广区')
                    ->description('仅一级菜单显示：下拉面板右侧的图片与文字链接（共 7 个大栏目可各配一组）')
                    ->visible(fn (Get $get) => blank($get('parent_id')))
                    ->schema([
                        Forms\Components\FileUpload::make('icon')
                            ->label('推广图片')
                            ->image()
                            ->disk('public')
                            ->directory('menu-promo')
                            ->visibility('public')
                            ->imageEditor()
                            ->maxSize(5120)
                            ->columnSpanFull()
                            ->helperText('建议尺寸约 445×195，支持 JPG / PNG / WebP'),
                        Forms\Components\TextInput::make('megamenu_image_alt')
                            ->label('图片 Alt 文本'),
                        Forms\Components\TextInput::make('megamenu_promo_text')
                            ->label('推广文字')
                            ->placeholder('如：了解更多关于 IPA'),
                        Forms\Components\TextInput::make('megamenu_promo_url')
                            ->label('推广链接')
                            ->placeholder('/about-the-ipa 或 https://...'),
                    ])
                    ->columns(2),
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
                            'showToggle' => $hasChildren && $depth < 2,
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
                Actions\CreateAction::make()
                    ->url(fn (Pages\ListMenuItems $livewire) => MenuItemResource::getUrl('create', filled($livewire->menuId)
                        ? ['menu_id' => $livewire->menuId]
                        : [])),
                Actions\Action::make('bulkDeleteSelected')
                    ->label('批量删除')
                    ->icon(Heroicon::Trash)
                    ->color('danger')
                    ->action(function (Pages\ListMenuItems $livewire) {
                        if ($livewire->getSelectedTableRecords()->isEmpty()) {
                            Notification::make()
                                ->title('未选择任何菜单项')
                                ->body('请先在表格左侧勾选要删除的菜单项，再点击「批量删除」。')
                                ->warning()
                                ->send();

                            return;
                        }

                        return $livewire->mountTableBulkAction('delete');
                    }),
                Actions\Action::make('importCategories')
                    ->label(function (Pages\ListMenuItems $livewire): string {
                        $label = '批量导入栏目';

                        if (filled($livewire->menuId)) {
                            $name = Menu::find($livewire->menuId)?->name;
                            if ($name) {
                                return "{$label} · {$name}";
                            }
                        }

                        return $label;
                    })
                    ->icon(Heroicon::Plus)
                    ->fillForm(fn (Pages\ListMenuItems $livewire): array => [
                        'menu_id' => $livewire->menuId ?: Menu::query()->value('id'),
                        'include_descendants' => true,
                        'all_selected' => false,
                        'category_ids' => [],
                    ])
                    ->form(function (Pages\ListMenuItems $livewire): array {
                        $menuLocked = filled($livewire->menuId);

                        return [
                            Forms\Components\Hidden::make('menu_id')
                                ->default($livewire->menuId)
                                ->dehydrated()
                                ->visible($menuLocked),
                            Forms\Components\Select::make('menu_id')
                                ->hiddenLabel()
                                ->options(Menu::all()->pluck('name', 'id'))
                                ->placeholder('请选择菜单')
                                ->required(! $menuLocked)
                                ->visible(! $menuLocked),
                            Forms\Components\CheckboxList::make('category_ids')
                                ->label('选择栏目（可多选）')
                                ->options(fn () => static::buildCategoryImportOptions())
                                ->columns(3)
                                ->required()
                                ->live(onBlur: true),
                            Forms\Components\Checkbox::make('all_selected')
                                ->label('全选下列栏目')
                                ->live()
                                ->afterStateUpdated(function (bool $state, Set $set) {
                                    $set('category_ids', $state ? array_keys(static::buildCategoryImportOptions()) : []);
                                }),
                            Forms\Components\Checkbox::make('include_descendants')
                                ->label('导入时包含选中栏目的所有子栏目')
                                ->helperText('仅影响提交导入结果，不会自动勾选列表中的其他栏目')
                                ->default(true),
                        ];
                    })
                    ->action(function (array $data) {
                        $menuId = $data['menu_id'];
                        $categoryIds = array_values(array_unique(array_map('intval', $data['category_ids'] ?? [])));
                        $includeDescendants = (bool) ($data['include_descendants'] ?? true);

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
                            $parentMenuItemId = ($category->parent_id && $category->parent_id !== 0)
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
            ->toolbarActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make()
                        ->label('批量删除'),
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

    private static function buildCategoryImportOptions(): array
    {
        $options = [];

        $append = function ($categories, string $prefix = '') use (&$append, &$options) {
            foreach ($categories as $category) {
                $options[$category->id] = $prefix.$category->name;
                $children = Category::where('parent_id', $category->id)->orderBy('sort_order')->get();
                if ($children->isNotEmpty()) {
                    $append($children, $prefix.'└─ ');
                }
            }
        };

        $append(Category::where('parent_id', 0)->orderBy('sort_order')->get());

        return $options;
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

        foreach (Category::where('parent_id', $categoryId)->orderBy('sort_order')->pluck('id') as $childId) {
            $ids[] = (int) $childId;
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
