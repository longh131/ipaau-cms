@php
    $hasTitle = filled($block['title'] ?? null);
    $hasHtml = filled(strip_tags((string) ($block['html'] ?? '')));
@endphp

@if($hasTitle || $hasHtml)
    <div class="about-rich-text text-left cms-body-block cms-body-block--rich-text">
        @if($hasTitle)
            <h3 class="cms-rich-text__title font-apex-book text-display-xl lg:text-display-2xl mb-0 text-center">
                <span class="text-secondary">{{ $block['title'] }}</span>
            </h3>
        @endif

        @if($hasHtml)
            <div
                @class([
                    'cms-page-content cms-rich-text__body text-[color:var(--ipa-color)] font-din',
                    'mt-8' => $hasTitle,
                ])
                data-rte="true"
            >
                {!! $block['html'] !!}
            </div>
        @endif
    </div>
@endif
