<?php

namespace App\Filament\Resources\PageComponentResource\Pages;

use App\Filament\Resources\PageComponentResource;
use App\Support\HomeSection\BasicContentSectionData;
use App\Support\HomeSection\CpdIntroSectionData;
use App\Support\HomeSection\CtaSectionData;
use App\Support\HomeSection\DiversitySectionData;
use App\Support\HomeSection\FaqSectionData;
use App\Support\HomeSection\FootnoteCardsSectionData;
use App\Support\HomeSection\NewsletterSectionData;
use App\Support\HomeSection\StatsSectionData;
use App\Support\HomeSection\TabbedContentSectionData;
use App\Support\HomeSection\TestimonialsSectionData;
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
            'hero', 'membership', 'about-intro' => BasicContentSectionData::forForm($stored),
            'cta-section' => CtaSectionData::forForm($stored),
            'footnote-cards' => FootnoteCardsSectionData::forForm($stored),
            'stats' => StatsSectionData::forForm($stored),
            'cpd-intro' => CpdIntroSectionData::forForm($stored),
            'tabbed-content' => TabbedContentSectionData::forForm($stored),
            'testimonials' => TestimonialsSectionData::forForm($stored),
            'diversity' => DiversitySectionData::forForm($stored),
            'faq' => FaqSectionData::forForm($stored),
            'newsletter' => NewsletterSectionData::forForm($stored),
            default => $stored ?? [],
        };

        if (in_array($type, HomeSectionTypes::BASIC_CONTENT_TYPES, true)) {
            $data['data'] = static::ensureTitleLinesForForm($data['data']);
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
            'hero', 'membership', 'about-intro' => BasicContentSectionData::forForm($this->getRecord()->data),
            'cta-section' => CtaSectionData::forForm($this->getRecord()->data),
            'footnote-cards' => FootnoteCardsSectionData::forForm($this->getRecord()->data),
            'stats' => StatsSectionData::forForm($this->getRecord()->data),
            'cpd-intro' => CpdIntroSectionData::forForm($this->getRecord()->data),
            'tabbed-content' => TabbedContentSectionData::forForm($this->getRecord()->data),
            'testimonials' => TestimonialsSectionData::forForm($this->getRecord()->data),
            'diversity' => DiversitySectionData::forForm($this->getRecord()->data),
            'faq' => FaqSectionData::forForm($this->getRecord()->data),
            'newsletter' => NewsletterSectionData::forForm($this->getRecord()->data),
            default => null,
        };

        if ($formData === null) {
            return;
        }

        if (in_array($type, HomeSectionTypes::BASIC_CONTENT_TYPES, true)) {
            $formData = static::ensureTitleLinesForForm($formData);
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

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private static function ensureTitleLinesForForm(array $data): array
    {
        if (($data['title_lines'] ?? []) === []) {
            $data['title_lines'] = [['text' => '']];
        }

        return $data;
    }
}
