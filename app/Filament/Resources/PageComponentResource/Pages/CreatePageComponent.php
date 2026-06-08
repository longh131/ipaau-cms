<?php

namespace App\Filament\Resources\PageComponentResource\Pages;

use App\Filament\Resources\PageComponentResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePageComponent extends CreateRecord
{
    protected static string $resource = PageComponentResource::class;
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('create');
    }
}