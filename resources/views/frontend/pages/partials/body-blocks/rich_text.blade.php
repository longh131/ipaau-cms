@php
    use App\Support\RichContent;

    $tagline = trim((string) ($block['tagline'] ?? ''));
    $hasTagline = filled($tagline);
    $hasTitle = filled($block['title'] ?? null);
    $hasHtml = RichContent::hasVisibleHtml((string) ($block['html'] ?? ''));
    $buttons = $block['buttons'] ?? [];
    $layout = $layout ?? 'default';
    $titleAlign = match ($block['title_align'] ?? 'center') {
        'left' => 'text-left',
        'right' => 'text-right',
        default => 'text-center',
    };
    $buttonAlignClass = match ($block['title_align'] ?? 'center') {
        'center' => 'justify-center',
        'right' => 'justify-end',
        default => 'justify-start',
    };
    $bodyAlign = ($layout === 'professional_assistance' && ($block['title_align'] ?? 'center') === 'center')
        ? 'text-center'
        : 'text-left';
@endphp

@if($hasTagline || $hasTitle || $hasHtml || $buttons !== [])
    <div @class([
        'about-rich-text cms-body-block cms-body-block--rich-text',
        $titleAlign,
    ])>
        @if($hasTagline)
            <span
                @class([
                    'eyebrow-md block',
                    'cms-tagline-before-title' => $hasTitle,
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

        @if($buttons !== [])
            <div class="basis-auto flex flex-col sm:flex-row {{ $buttonAlignClass }} flex-wrap gap-6 mt-12 mb-6">
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
    </div>
@endif
