<section
    data-type="cardListCurated"
    data-index="4"
    class="cms-governance-module cms-governance-card-list"
>
    <div class="inner container px-4 md:px-10 mx-auto py-16">
        @include('frontend.pages.partials.shared.card-list-curated', [
            'sectionTitle' => $sectionTitle ?? '',
            'cardItems' => $cardItems ?? [],
        ])
    </div>
</section>
