@php
    use App\Support\RichContent;

    $bodyHtml = (string) ($block['body_html'] ?? '');
    $tagline = trim((string) ($block['tagline'] ?? ''));
    $title = trim((string) ($block['title'] ?? ''));
    $hasBody = RichContent::hasVisibleHtml($bodyHtml);
    $hasTagline = filled($tagline);
    $hasTitle = filled($title);
    $layout = $layout ?? 'default';
    $isCentered = $layout === 'professional_assistance';
@endphp

@if($hasTagline || $hasTitle || $hasBody)
    <section
        @class([
            'cms-body-block cms-body-block--html-body',
            'cms-governance-module cms-general-secondary-module' => $layout === 'general_secondary',
            'cms-html-body--professional-assistance' => $isCentered,
        ])
    >
        <div class="inner container px-4 md:px-10 mx-auto">
            @if($hasTagline || $hasTitle)
                <div @class([
                    'mb-8 max-w-5xl mx-auto',
                    $isCentered ? 'text-center' : 'text-left',
                ])>
                    @if($hasTagline)
                        <span
                            class="eyebrow-md block {{ $hasTitle ? 'mb-4' : '' }}"
                            style="
                                --ipa-color-light: oklch(0.4867 0.1803 336.11);
                                --ipa-color-dark: oklch(0.8944 0.0357 331.62);
                                color: var(--ipa-color-light);
                            "
                        >{{ $tagline }}</span>
                    @endif

                    @if($hasTitle)
                        <h3 @class([
                            'cms-section-title mb-0',
                            $isCentered ? 'text-center' : 'text-left',
                        ])>
                            <span class="text-secondary">{{ $title }}</span>
                        </h3>
                    @endif
                </div>
            @endif

            @if($hasBody)
                <div @class([
                    'about-rich-text cms-page-content cms-basic-content__body cms-basic-content__body--html font-din text-[color:var(--ipa-color)]',
                    'text-center' => $isCentered,
                ])>
                    {!! $bodyHtml !!}
                </div>
            @endif
        </div>
    </section>
@endif
