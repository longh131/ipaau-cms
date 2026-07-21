@php
    use App\Support\PageTemplate\Templates\GovernancePageData;

    $cardItems = $cardItems ?? [];
    $initialVisible = GovernancePageData::CARD_LIST_INITIAL_VISIBLE;
    $hasHiddenItems = count($cardItems) > $initialVisible;
@endphp

<section
    data-type="cardListCurated"
    data-index="4"
    @class([
        'cms-governance-module cms-governance-card-list',
        'cms-news-list-curated--expandable' => $hasHiddenItems,
    ])
>
    <div class="inner container px-4 md:px-10 mx-auto py-16">
        @include('frontend.pages.partials.shared.card-list-curated', [
            'sectionTitle' => $sectionTitle ?? '',
            'cardItems' => $cardItems,
            'initialVisible' => $initialVisible,
            'viewMoreLabel' => $viewMoreLabel ?? '查看更多',
        ])
    </div>
</section>
