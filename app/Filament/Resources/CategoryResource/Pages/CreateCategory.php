<?php

namespace App\Filament\Resources\CategoryResource\Pages;

use App\Filament\Resources\CategoryResource;
use App\Filament\Resources\CategoryResource\Pages\Concerns\NormalizesCategoryFormData;
use Filament\Resources\Pages\CreateRecord;

class CreateCategory extends CreateRecord
{
    use NormalizesCategoryFormData;

    protected static string $resource = CategoryResource::class;
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('create');
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return $this->normalizeCategoryStorageData($data);
    }
}