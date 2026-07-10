@php
    $heading = trim((string) ($pageView['heading'] ?? ''));
    $summary = trim((string) ($pageView['summary'] ?? ''));
    $sections = $pageView['sections'] ?? [];
    $hasBreadcrumbs = $hasBreadcrumbs ?? false;
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
            'pb-12 cms-page-content-section cms-general-secondary-page',
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
            @if(($section['type'] ?? '') === 'content_block')
                @include('frontend.pages.partials.shared.content-block', [
                    'block' => $section,
                    'sectionClass' => 'cms-governance-module cms-general-secondary-module cms-governance-content-block',
                ])
            @elseif(($section['type'] ?? '') === 'faq')
                <div class="cms-governance-module cms-general-secondary-module">
                    @include('frontend.pages.partials.body-blocks.faq', [
                        'block' => $section,
                    ])
                </div>
            @elseif(($section['type'] ?? '') === 'news_list')
                @include('frontend.pages.partials.body-blocks.news_list', [
                    'block' => $section,
                ])
            @endif
        @endforeach
    </section>
@endif
