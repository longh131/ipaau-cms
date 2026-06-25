<?php

namespace Database\Seeders;

use App\Models\PageComponent;
use App\Support\HomeSectionTypes;
use Illuminate\Database\Seeder;

class HomeSectionSeeder extends Seeder
{
    public function run(): void
    {
        foreach (HomeSectionTypes::definitions() as $key => $definition) {
            PageComponent::firstOrCreate(
                [
                    'page_slug' => HomeSectionTypes::PAGE_SLUG,
                    'component_type' => $key,
                ],
                [
                    'sort_order' => HomeSectionTypes::defaultSortOrder($key),
                    'data' => HomeSectionTypes::defaultData($key),
                    'is_active' => true,
                ]
            );
        }
    }
}
