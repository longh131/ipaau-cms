<?php

namespace App\Filament\Resources\MenuItemResource\Pages;

use App\Filament\Resources\MenuItemResource;
use App\Support\MenuItemLink;
use Filament\Resources\Pages\EditRecord;

class EditMenuItem extends EditRecord
{
    protected static string $resource = MenuItemResource::class;

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
}
