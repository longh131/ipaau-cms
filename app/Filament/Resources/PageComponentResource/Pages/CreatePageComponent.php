<?php

namespace App\Filament\Resources\PageComponentResource\Pages;

use App\Filament\Resources\PageComponentResource;
use App\Support\HomeSection\FootnoteCardsSectionData;
use App\Support\HomeSection\HeroSectionData;
use App\Support\HomeSectionTypes;
use Filament\Resources\Pages\CreateRecord;
use Filament\Schemas\Schema;

class CreatePageComponent extends CreateRecord
{
    protected static string $resource = PageComponentResource::class;

    public function form(Schema $schema): Schema
    {
        return $schema->components(
            PageComponentResource::formComponents(includeKeyValue: true),
        );
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return static::normalizeStructuredData($data);
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public static function normalizeStructuredData(array $data): array
    {
        $type = $data['component_type'] ?? null;

        if ($type === 'hero') {
            $data['data'] = HeroSectionData::forStorage(is_array($data['data'] ?? null) ? $data['data'] : []);
        }

        if ($type === 'footnote-cards') {
            $data['data'] = FootnoteCardsSectionData::forStorage(is_array($data['data'] ?? null) ? $data['data'] : []);
        }

        return $data;
    }
}
