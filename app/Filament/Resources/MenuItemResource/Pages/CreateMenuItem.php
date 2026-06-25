<?php

namespace App\Filament\Resources\MenuItemResource\Pages;

use App\Filament\Resources\MenuItemResource;
use App\Support\MenuItemLink;
use Filament\Resources\Pages\CreateRecord;

class CreateMenuItem extends CreateRecord
{
    protected static string $resource = MenuItemResource::class;

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
