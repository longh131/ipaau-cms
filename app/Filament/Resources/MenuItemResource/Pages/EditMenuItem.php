<?php

namespace App\Filament\Resources\MenuItemResource\Pages;

use App\Filament\Resources\MenuItemResource;
use App\Filament\Resources\MenuItemResource\Pages\Concerns\HasMenuItemBreadcrumbs;
use App\Support\MenuItemLink;
use Filament\Resources\Pages\EditRecord;

class EditMenuItem extends EditRecord
{
    use HasMenuItemBreadcrumbs;

    protected static string $resource = MenuItemResource::class;

    public function getResourceBreadcrumbs(): array
    {
        return $this->menuItemBreadcrumbTrail(
            menuId: $this->getRecord()->menu_id,
            includeList: true,
        );
    }

    public function getBreadcrumb(): string
    {
        return '编辑';
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $record = $this->getRecord();
        $data['link_type'] = MenuItemLink::inferType($record);
        $data['link_id'] = MenuItemLink::inferLinkId($record);

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        return MenuItemLink::apply($data);
    }

    protected function getRedirectUrl(): string
    {
        $menuId = $this->getRecord()->menu_id;

        return MenuItemResource::getUrl('index', $menuId ? ['menu_id' => $menuId] : []);
    }
}
