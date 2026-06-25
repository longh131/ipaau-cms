<?php

namespace App\Services;

use App\Models\PageComponent;
use App\Support\HomeSection\FootnoteCardsSectionData;
use App\Support\HomeSection\HeroSectionData;
use App\Support\HomeSectionTypes;

class PageComponentService
{
    public function getHomeComponent(string $type): ?PageComponent
    {
        return PageComponent::query()
            ->where('page_slug', HomeSectionTypes::PAGE_SLUG)
            ->where('component_type', $type)
            ->where('is_active', true)
            ->first();
    }

    /**
     * @return array{
     *     tagline: string,
     *     title_lines: array<int, string>,
     *     description: string,
     *     buttons: array<int, array{label: string, url: string, target: ?string, style: string}>
     * }
     */
    public function getHeroData(): array
    {
        $component = $this->getHomeComponent('hero');

        return HeroSectionData::forFrontend($component?->data);
    }

    /**
     * @return array{items: array<int, array{title: string, url: ?string, image_desktop: ?string, image_mobile: ?string, show_arrow: bool}>}
     */
    public function getFootnoteCardsData(): array
    {
        $component = $this->getHomeComponent('footnote-cards');

        return FootnoteCardsSectionData::forFrontend($component?->data);
    }
}
