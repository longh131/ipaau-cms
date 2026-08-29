@php
    $heading = trim((string) ($pageView['heading'] ?? ''));
    $summary = trim((string) ($pageView['summary'] ?? ''));
    $hasBreadcrumbs = $hasBreadcrumbs ?? false;
    $sections = $pageView['sections'] ?? [];
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
            'cms-page-content-section cms-governance-page',
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

        @foreach ($sections as $section)
            @if(($section['type'] ?? '') === 'bento')
                @include('frontend.pages.partials.governance.bento-box', [
                    'bentoStyle' => $section['bento_style'] ?? 'five',
                    'bentoCards' => $section['bento_cards'] ?? [],
                ])
            @elseif(($section['type'] ?? '') === 'content_block')
                @include('frontend.pages.partials.shared.content-block', [
                    'block' => $section,
                    'sectionClass' => 'cms-governance-module cms-governance-content-block',
                ])
            @elseif(($section['type'] ?? '') === 'html_body')
                @include('frontend.pages.partials.body-blocks.html_body', [
                    'block' => $section,
                    'layout' => 'general_secondary',
                ])
            @elseif(($section['type'] ?? '') === 'card_list_curated')
                @include('frontend.pages.partials.governance.card-list-curated', [
                    'sectionTitle' => $section['section_title'] ?? '',
                    'cardItems' => $section['items'] ?? [],
                ])
            @endif
        @endforeach

        @include('frontend.pages.partials.page-content-footer-spacer')
    </section>
@endif
