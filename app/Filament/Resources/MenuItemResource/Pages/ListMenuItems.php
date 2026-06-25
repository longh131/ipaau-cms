<?php

namespace App\Filament\Resources\MenuItemResource\Pages;

use App\Filament\Resources\MenuItemResource;
use App\Filament\Resources\MenuItemResource\Pages\Concerns\HasMenuItemBreadcrumbs;
use App\Models\MenuItem;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\EmbeddedTable;
use Filament\Schemas\Components\RenderHook;
use Filament\Schemas\Components\View as SchemaView;
use Filament\Schemas\Schema;
use Filament\View\PanelsRenderHook;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection as SupportCollection;
use Livewire\Attributes\Url;

class ListMenuItems extends ListRecords
{
    use HasMenuItemBreadcrumbs;

    protected static string $resource = MenuItemResource::class;

    #[Url(as: 'menu_id')]
    public ?int $menuId = null;

    protected function getTableQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $query = parent::getTableQuery();

        if ($this->menuId) {
            $query->where('menu_id', $this->menuId);
        }

        return $query;
    }

    public function getResourceBreadcrumbs(): array
    {
        return $this->menuItemBreadcrumbTrail();
    }

    public function getBreadcrumb(): ?string
    {
        return '菜单项';
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                $this->getTabsContentComponent(),
                RenderHook::make(PanelsRenderHook::RESOURCE_PAGES_LIST_RECORDS_TABLE_BEFORE),
                EmbeddedTable::make(),
                RenderHook::make(PanelsRenderHook::RESOURCE_PAGES_LIST_RECORDS_TABLE_AFTER),
                SchemaView::make('filament.resources.menu-item.partials.tree-script'),
            ]);
    }

    public function getTableRecords(): Collection|LengthAwarePaginator
    {
        if (! $this->menuId) {
            return parent::getTableRecords();
        }

        $allItems = MenuItem::where('menu_id', $this->menuId)
            ->orderBy('parent_id')
            ->orderBy('sort_order')
            ->get();

        $sortedItems = $this->sortByTree($allItems);

        $paginator = parent::getTableRecords();

        if ($paginator instanceof LengthAwarePaginator) {
            return new LengthAwarePaginator(
                $sortedItems,
                $sortedItems->count(),
                max($paginator->perPage(), $sortedItems->count()),
                $paginator->currentPage(),
                ['path' => $paginator->path()]
            );
        }

        return $sortedItems;
    }

    private function sortByTree(Collection $records): Collection
    {
        if ($records->isEmpty()) {
            return $records;
        }

        $byParent = $records->groupBy(function ($item) {
            return $item->parent_id ?? 'root';
        });

        $sorted = new SupportCollection();

        $addChildren = function ($parentId) use ($byParent, &$sorted, &$addChildren) {
            $key = $parentId === null ? 'root' : (string) $parentId;
            $children = $byParent->get($key, collect())->sortBy('sort_order');

            foreach ($children as $child) {
                $sorted->push($child);
                $addChildren($child->id);
            }
        };

        $addChildren(null);

        return new Collection($sorted->all());
    }
}
