@php
    use App\Support\RichContent;

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

@if($hasTitle || $hasHtml)
    <div @class([
        'about-rich-text cms-body-block cms-body-block--rich-text',
        $titleAlign,
    ])>
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
                    'mt-8' => $hasTitle,
                ])
                data-rte="true"
            >
                {!! $block['html'] !!}
            </div>
        @endif
    </div>
@endif
