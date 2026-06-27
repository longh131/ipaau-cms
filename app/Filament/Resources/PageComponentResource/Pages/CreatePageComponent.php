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
        $stored = is_array($data['data'] ?? null) ? $data['data'] : [];

        if (in_array($type, HomeSectionTypes::BASIC_CONTENT_TYPES, true)) {
            $data['data'] = match ($type) {
                'cta-section' => CtaSectionData::forStorage($stored),
                default => BasicContentSectionData::forStorage($stored),
            };
        }

        if ($type === 'footnote-cards') {
            $data['data'] = FootnoteCardsSectionData::forStorage($stored);
        }

        if ($type === 'stats') {
            $data['data'] = StatsSectionData::forStorage($stored);
        }

        if ($type === 'cpd-intro') {
            $data['data'] = CpdIntroSectionData::forStorage($stored);
        }

        if ($type === 'tabbed-content') {
            $data['data'] = TabbedContentSectionData::forStorage($stored);
        }

        if ($type === 'testimonials') {
            $data['data'] = TestimonialsSectionData::forStorage($stored);
        }

        if ($type === 'diversity') {
            $data['data'] = DiversitySectionData::forStorage($stored);
        }

        if ($type === 'faq') {
            $data['data'] = FaqSectionData::forStorage($stored);
        }

        if ($type === 'newsletter') {
            $data['data'] = NewsletterSectionData::forStorage($stored);
        }

        return $data;
    }
}
