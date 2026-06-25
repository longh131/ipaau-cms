<?php

namespace App\Filament\Resources\MenuItemResource\Pages\Concerns;

use App\Filament\Resources\MenuItemResource;
use App\Filament\Resources\MenuResource;

trait HasMenuItemBreadcrumbs
{
    /**
     * @return array<string, string>
     */
    protected function menuItemBreadcrumbTrail(?int $menuId = null, bool $includeList = false): array
    {
        $breadcrumbs = [
            MenuResource::getUrl('index') => MenuResource::getNavigationLabel(),
        ];

        if ($includeList && $menuId) {
            $breadcrumbs[MenuItemResource::getUrl('index', ['menu_id' => $menuId])] = '菜单项';
        }

        return $breadcrumbs;
    }
}
