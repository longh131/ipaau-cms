@php
    use App\Support\RichContent;

    $heading = trim((string) ($pageView['heading'] ?? ''));
    $summaryHtml = (string) ($pageView['summary_html'] ?? '');
    $hasSummary = RichContent::hasVisibleHtml($summaryHtml);
    $buttons = $pageView['buttons'] ?? [];
    $sections = $pageView['sections'] ?? [];
    $hasBreadcrumbs = $hasBreadcrumbs ?? false;
    $hasPageHeader = filled($heading) || $hasSummary || $buttons !== [];
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
            'cms-page-content-section cms-general-secondary-page',
            'cms-page-content-section--with-breadcrumb' => $hasBreadcrumbs,
            'pt-28' => ! $hasBreadcrumbs,
        ])
        style="{{ $cmsSectionStyle }}"
    >
        @if($hasPageHeader)
            <div class="cms-governance-header">
                <div class="inner container px-4 md:px-10 mx-auto cms-governance-header__inner">
                    <header class="cms-governance-header__header">
                        @if(filled($heading))
                            <h1 class="cms-governance-header__title font-apex-book cms-section-title text-secondary mb-0">
                                {{ $heading }}
                            </h1>
                        @endif

                        @if($hasSummary)
                            <div
                                class="cms-governance-header__summary cms-page-content cms-rich-text__body font-din text-primary leading-relaxed"
                                data-rte="true"
                            >
                                {!! $summaryHtml !!}
                            </div>
                        @endif

                        @if($buttons !== [])
                            <div class="cms-governance-header__actions flex flex-col sm:flex-row justify-center flex-wrap gap-6">
                                @foreach ($buttons as $button)
                                    <x-cta-button
                                        :label="$button['label']"
                                        :url="$button['url']"
                                        :style="$button['style']"
                                        :target="filled($button['target'] ?? null) ? $button['target'] : null"
                                    />
                                @endforeach
                            </div>
                        @endif
                    </header>
                </div>
            </div>
        @endif

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
                        'layout' => 'general_secondary',
                    ])
                </div>
            @elseif(($section['type'] ?? '') === 'news_list_a')
                @include('frontend.pages.partials.body-blocks.news_list', [
                    'block' => $section,
                    'layout' => 'default',
                    'wrapModule' => true,
                ])
            @elseif(($section['type'] ?? '') === 'news_list')
                @include('frontend.pages.partials.body-blocks.news_list', [
                    'block' => $section,
                    'layout' => 'general_secondary',
                ])
            @elseif(($section['type'] ?? '') === 'stats')
                @include('frontend.pages.partials.body-blocks.stats', [
                    'block' => $section,
                    'layout' => 'general_secondary',
                ])
            @elseif(($section['type'] ?? '') === 'testimonials')
                @include('frontend.pages.partials.body-blocks.testimonials', [
                    'block' => $section,
                    'layout' => 'general_secondary',
                ])
            @elseif(($section['type'] ?? '') === 'newsletter')
                @include('frontend.pages.partials.body-blocks.newsletter', [
                    'block' => $section,
                    'layout' => 'general_secondary',
                ])
            @elseif(($section['type'] ?? '') === 'html_body')
                @include('frontend.pages.partials.body-blocks.html_body', [
                    'block' => $section,
                    'layout' => 'general_secondary',
                ])
            @elseif(($section['type'] ?? '') === 'left_right_layout')
                @include('frontend.pages.partials.body-blocks.left_right_layout', [
                    'block' => $section,
                ])
            @elseif(($section['type'] ?? '') === 'tabbed_content')
                @include('frontend.pages.partials.body-blocks.tabbed_content', [
                    'block' => $section,
                ])
            @elseif(($section['type'] ?? '') === 'media_split')
                @include('frontend.pages.partials.body-blocks.media_split', [
                    'block' => $section,
                    'layout' => 'general_secondary',
                ])
            @endif
        @endforeach

        @include('frontend.pages.partials.page-content-footer-spacer')
    </section>
@endif
