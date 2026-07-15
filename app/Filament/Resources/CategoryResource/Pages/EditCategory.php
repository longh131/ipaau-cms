<?php

namespace App\Filament\Resources\CategoryResource\Pages;

use App\Filament\Resources\CategoryResource;
use App\Filament\Resources\CategoryResource\Pages\Concerns\NormalizesCategoryFormData;
use Filament\Resources\Pages\EditRecord;

class EditCategory extends EditRecord
{
    use NormalizesCategoryFormData;

    protected static string $resource = CategoryResource::class;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        return $this->normalizeCategoryFormData($data);
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        return $this->normalizeCategoryStorageData($data);
    }
}