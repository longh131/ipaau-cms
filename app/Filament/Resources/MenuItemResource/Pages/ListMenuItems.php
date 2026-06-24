<?php

namespace App\Filament\Resources\MenuItemResource\Pages;

use App\Filament\Resources\MenuItemResource;
use App\Models\MenuItem;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection as SupportCollection;

class ListMenuItems extends ListRecords
{
    protected static string $resource = MenuItemResource::class;

    public function getTableRecords(): Collection|LengthAwarePaginator
    {
        $menuId = request()->query('menu_id');
        if (!$menuId) {
            return parent::getTableRecords();
        }

        $allItems = MenuItem::where('menu_id', $menuId)
            ->orderBy('parent_id')
            ->orderBy('sort_order')
            ->get();

        $sortedItems = $this->sortByTree($allItems);

        $paginator = parent::getTableRecords();
        
        if ($paginator instanceof LengthAwarePaginator) {
            return new LengthAwarePaginator(
                $sortedItems,
                $paginator->total(),
                $paginator->perPage(),
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
