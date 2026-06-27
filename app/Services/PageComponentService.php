<?php

namespace App\Services;

use App\Models\PageComponent;
use App\Support\HomeSection\CpdIntroSectionData;
use App\Support\HomeSection\FootnoteCardsSectionData;
use App\Support\HomeSection\HeroSectionData;
use App\Support\HomeSection\MembershipSectionData;
use App\Support\HomeSection\StatsSectionData;
use App\Support\HomeSection\TabbedContentSectionData;
use App\Support\HomeSection\TestimonialsSectionData;
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

    /**
     * @return array{
     *     tagline: string,
     *     title_lines: array<int, string>,
     *     description: string,
     *     buttons: array<int, array{label: string, url: string, target: ?string, style: string}>
     * }
     */
    public function getMembershipData(): array
    {
        $component = $this->getHomeComponent('membership');

        return MembershipSectionData::forFrontend($component?->data);
    }

    /**
     * @return array{items: array<int, array{number: string, title: string, content: string}>}
     */
    public function getStatsData(): array
    {
        $component = $this->getHomeComponent('stats');

        return StatsSectionData::forFrontend($component?->data);
    }

    /**
     * @return array{html: string}
     */
    public function getCpdIntroData(): array
    {
        $component = $this->getHomeComponent('cpd-intro');

        return CpdIntroSectionData::forFrontend($component?->data);
    }

    /**
     * @return array{tabs: array<int, array{tab_label: string, tagline: string, title: string, description: string, button_label: string, url: ?string, image: ?string}>}
     */
    public function getTabbedContentData(): array
    {
        $component = $this->getHomeComponent('tabbed-content');

        return TabbedContentSectionData::forFrontend($component?->data);
    }

    /**
     * @return array{items: array<int, array{title: string, title_lines: array<int, string>, content: string, image: ?string}>}
     */
    public function getTestimonialsData(): array
    {
        $component = $this->getHomeComponent('testimonials');

        return TestimonialsSectionData::forFrontend($component?->data);
    }
}
