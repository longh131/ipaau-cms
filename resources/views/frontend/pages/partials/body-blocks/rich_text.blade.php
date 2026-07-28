@php
    use App\Support\RichContent;

    $tagline = trim((string) ($block['tagline'] ?? ''));
    $hasTagline = filled($tagline);
    $hasTitle = filled($block['title'] ?? null);
    $hasHtml = RichContent::hasVisibleHtml((string) ($block['html'] ?? ''));
    $layout = $layout ?? 'default';
    $titleAlign = match ($block['title_align'] ?? 'center') {
        'left' => 'text-left',
        'right' => 'text-right',
        default => 'text-center',
    };
    $bodyAlign = ($layout === 'professional_assistance' && ($block['title_align'] ?? 'center') === 'center')
        ? 'text-center'
        : 'text-left';
@endphp

@if($hasTagline || $hasTitle || $hasHtml)
    <div @class([
        'about-rich-text cms-body-block cms-body-block--rich-text',
        $titleAlign,
    ])>
        @if($hasTagline)
            <span
                @class([
                    'eyebrow-md block',
                    'mb-4' => $hasTitle,
                    $titleAlign,
                ])
                style="
                    --ipa-color-light: oklch(0.4867 0.1803 336.11);
                    --ipa-color-dark: oklch(0.8944 0.0357 331.62);
                    color: var(--ipa-color-light);
                "
            >{{ $tagline }}</span>
        @endif

        @if($hasTitle)
            <h3 @class([
                'cms-rich-text__title cms-section-title font-apex-book mb-0',
                $titleAlign,
            ])>
                <span class="text-secondary">{{ $block['title'] }}</span>
            </h3>
        @endif

        @if($hasHtml)
            <div
                @class([
                    'cms-page-content cms-rich-text__body text-[color:var(--ipa-color)] font-din',
                    $bodyAlign,
                    'mt-8' => $hasTitle && ! $hasTagline,
                    'mt-4' => $hasTagline,
                ])
                data-rte="true"
            >
                {!! $block['html'] !!}
            </div>
        @endif
    </div>
@endif
