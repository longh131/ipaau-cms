@php
    /** @var array{
     *     section_title: string,
     *     items: array<int, array{title: string, url: string, target: string}>
     * } $block */
    $sectionTitle = trim((string) ($block['section_title'] ?? ''));
    $cardItems = $block['items'] ?? [];
@endphp

@if(filled($sectionTitle) || $cardItems !== [])
    <section
        data-type="cardListCurated"
        class="cms-body-block cms-body-block--card-list bg-[color:var(--bg-color)]"
        style="
            --bg-color: transparent;
            --ipa-color-light: oklch(0.464 0 0);
            --ipa-color-dark: oklch(1 0 0);
            --light-or-dark: light;
            color: var(--ipa-color-light);
        "
    >
        <div class="inner container px-4 md:px-10 mx-auto py-16">
            @include('frontend.pages.partials.shared.card-list-curated', [
                'sectionTitle' => $sectionTitle,
                'cardItems' => $cardItems,
            ])
        </div>
    </section>
@endif
