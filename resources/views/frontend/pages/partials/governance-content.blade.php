@php
    $heading = trim((string) ($pageView['heading'] ?? ''));
    $summary = trim((string) ($pageView['summary'] ?? ''));
    $hasBreadcrumbs = $hasBreadcrumbs ?? false;
    $contentBlock = $pageView['content_block'] ?? [];
    $cardListTitle = trim((string) ($pageView['card_list_title'] ?? ''));
    $cardListItems = $pageView['card_list_items'] ?? [];
    $showContentBlock = filled($contentBlock['title'] ?? null)
        || filled(strip_tags((string) ($contentBlock['content_html'] ?? '')))
        || filled($contentBlock['button'] ?? null);
    $showCardList = filled($cardListTitle) || $cardListItems !== [];
    $cmsSectionStyle = '
        --bg-color: transparent;
        --ipa-color-light: oklch(0.464 0 0);
        --ipa-color-dark: oklch(1 0 0);
        --light-or-dark: light;
        color: var(--ipa-color-light);
    ';
@endphp

@if($pageView['has_content'] ?? false)
    <section
        data-type="cmsPageContent"
        @class([
            'pb-12 cms-page-content-section cms-governance-page',
            'cms-page-content-section--with-breadcrumb' => $hasBreadcrumbs,
            'pt-28' => ! $hasBreadcrumbs,
        ])
        style="{{ $cmsSectionStyle }}"
    >
        <div class="cms-governance-header">
            <div class="inner container px-4 md:px-10 mx-auto cms-governance-header__inner">
                <header class="cms-governance-header__header">
                    @if(filled($heading))
                        <h1 class="cms-governance-header__title font-apex-book cms-section-title text-secondary mb-0">
                            {{ $heading }}
                        </h1>
                    @endif

                    @if(filled($summary))
                        <p class="cms-governance-header__summary font-din text-primary leading-relaxed">
                            {{ $summary }}
                        </p>
                    @endif
                </header>
            </div>
        </div>

        @if($pageView['has_bento'] ?? false)
            @include('frontend.pages.partials.governance.bento-box', [
                'bentoStyle' => $pageView['bento_style'] ?? 'five',
                'bentoCards' => $pageView['bento_cards'] ?? [],
            ])
        @endif

        @if($showContentBlock)
            @include('frontend.pages.partials.governance.content-block', [
                'block' => $contentBlock,
            ])
        @endif

        @if($showCardList)
            @include('frontend.pages.partials.governance.card-list-curated', [
                'sectionTitle' => $cardListTitle,
                'cardItems' => $cardListItems,
            ])
        @endif
    </section>
@endif
