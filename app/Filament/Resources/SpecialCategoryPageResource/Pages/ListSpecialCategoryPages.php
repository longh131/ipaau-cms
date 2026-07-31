<?php

namespace App\Filament\Resources\SpecialCategoryPageResource\Pages;

use App\Filament\Resources\SpecialCategoryPageResource;
use Filament\Resources\Pages\ListRecords;

class ListSpecialCategoryPages extends ListRecords
{
    protected static string $resource = SpecialCategoryPageResource::class;

    public function getTitle(): string
    {
        return '功能栏目页';
    }
}
