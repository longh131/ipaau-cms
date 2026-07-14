@php
    $heading = trim((string) ($pageView['heading'] ?? ''));
    $summary = trim((string) ($pageView['summary'] ?? ''));
    $bodyHtml = (string) ($pageView['body_html'] ?? '');
    $hasBody = filled(strip_tags($bodyHtml));
    $hasBreadcrumbs = $hasBreadcrumbs ?? false;
@endphp

@if($pageView['has_content'] ?? false)
    <section
        data-type="basicContent"
        @class([
            'cms-page-content-section cms-basic-content-section',
            'cms-page-content-section--with-breadcrumb' => $hasBreadcrumbs,
            'pt-28' => ! $hasBreadcrumbs,
        ])
        style="
            --bg-color: transparent;
            --ipa-color-light: oklch(0.464 0 0);
            --ipa-color-dark: oklch(1 0 0);
            --light-or-dark: light;
            color: var(--ipa-color-light);
        "
    >
        <div class="inner container px-4 md:px-10 mx-auto cms-basic-content__inner">
            <header class="cms-basic-content__header">
                @if(filled($heading))
                    <h1 class="cms-basic-content__title font-apex-book cms-section-title text-secondary mb-0">
                        {{ $heading }}
                    </h1>
                @endif

                @if(filled($summary))
                    <p class="cms-basic-content__summary font-din text-primary leading-relaxed">
                        {{ $summary }}
                    </p>
                @endif
            </header>

            @if($hasBody)
                <div class="about-rich-text cms-page-content cms-basic-content__body cms-basic-content__body--html font-din text-[color:var(--ipa-color)]">
                    {!! $bodyHtml !!}
                </div>
            @endif
        </div>

        @include('frontend.pages.partials.page-content-footer-spacer')
    </section>
@endif
