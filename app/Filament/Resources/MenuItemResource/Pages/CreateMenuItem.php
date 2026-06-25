<?php

namespace App\Filament\Resources\MenuItemResource\Pages;

use App\Filament\Resources\MenuItemResource;
use App\Filament\Resources\MenuItemResource\Pages\Concerns\HasMenuItemBreadcrumbs;
use App\Support\MenuItemLink;
use Filament\Resources\Pages\CreateRecord;

class CreateMenuItem extends CreateRecord
{
    use HasMenuItemBreadcrumbs;

    protected static string $resource = MenuItemResource::class;

    public function mount(): void
    {
        parent::mount();

        if ($menuId = request()->integer('menu_id')) {
            $this->form->fill([
                'menu_id' => $menuId,
            ]);
        }
    }

    public function getResourceBreadcrumbs(): array
    {
        $menuId = request()->query('menu_id') ?? $this->data['menu_id'] ?? null;

        return $this->menuItemBreadcrumbTrail(
            menuId: $menuId ? (int) $menuId : null,
            includeList: true,
        );
    }

    public function getBreadcrumb(): string
    {
        return '新建';
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return MenuItemLink::apply($data);
    }

    protected function getRedirectUrl(): string
    {
        $menuId = $this->record->menu_id;

        return MenuItemResource::getUrl('index', $menuId ? ['menu_id' => $menuId] : []);
    }
}
