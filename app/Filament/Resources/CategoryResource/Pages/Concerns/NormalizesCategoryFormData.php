<?php

namespace App\Filament\Resources\CategoryResource\Pages\Concerns;

use App\Filament\Resources\CategoryResource;

trait NormalizesCategoryFormData
{
    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function normalizeCategoryFormData(array $data): array
    {
        return CategoryResource::normalizeFormData($data);
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function normalizeCategoryStorageData(array $data): array
    {
        return CategoryResource::normalizeStorageData($data);
    }
}
