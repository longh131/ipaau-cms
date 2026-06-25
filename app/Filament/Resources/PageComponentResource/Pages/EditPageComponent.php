<?php

namespace App\Filament\Resources\PageComponentResource\Pages;

use App\Filament\Resources\PageComponentResource;
use App\Support\HomeSection\FootnoteCardsSectionData;
use App\Support\HomeSection\HeroSectionData;
use App\Support\HomeSectionTypes;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Schema;

class EditPageComponent extends EditRecord
{
    protected static string $resource = PageComponentResource::class;

    public function form(Schema $schema): Schema
    {
        return $schema->components(
            PageComponentResource::formComponents(
                includeKeyValue: ! HomeSectionTypes::isStructured($this->getRecord()->component_type),
            ),
        );
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $type = $data['component_type'] ?? null;
        $stored = is_array($data['data'] ?? null) ? $data['data'] : null;

        $data['data'] = match ($type) {
            'hero' => HeroSectionData::forForm($stored),
            'footnote-cards' => FootnoteCardsSectionData::forForm($stored),
            default => $stored ?? [],
        };

        if ($type === 'hero' && ($data['data']['title_lines'] ?? []) === []) {
            $data['data']['title_lines'] = [['text' => '']];
        }

        return $data;
    }

    protected function afterFill(): void
    {
        $type = $this->getRecord()->component_type;

        if (! HomeSectionTypes::isStructured($type)) {
            return;
        }

        $formData = match ($type) {
            'hero' => HeroSectionData::forForm($this->getRecord()->data),
            'footnote-cards' => FootnoteCardsSectionData::forForm($this->getRecord()->data),
            default => null,
        };

        if ($formData === null) {
            return;
        }

        if ($type === 'hero' && ($formData['title_lines'] ?? []) === []) {
            $formData['title_lines'] = [['text' => '']];
        }

        $this->form->fillPartially([
            'data' => $formData,
        ], ['data']);
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        return CreatePageComponent::normalizeStructuredData($data);
    }
}
