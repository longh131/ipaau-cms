@props([
    'tab',
    'hidden' => false,
])

@php
    /** @var array{
     *     tab_label: string,
     *     tagline: string,
     *     title: string,
     *     content_html: string,
     *     button_label: string,
     *     url: ?string
     * } $tab */
    $contentHtml = (string) ($tab['content_html'] ?? '');
    $hasContent = \App\Support\RichContent::hasVisibleHtml($contentHtml);
@endphp

<div
    data-type="tab-content"
    @class([
        'cms-tabbed-content__panel space-y-8 grow flex flex-col justify-start w-full',
        'hidden' => $hidden,
    ])
>
    <div class="text-left w-full">
        @if(filled($tab['tagline'] ?? null))
            <span
                class="eyebrow-xl"
                style="
                    --ipa-color-light: oklch(0.4867 0.1803 336.11);
                    --ipa-color-dark: oklch(0.8944 0.0357 331.62);
                    color: var(--ipa-color-light);
                "
            >{{ $tab['tagline'] }}</span>
        @endif

        @if(filled($tab['title'] ?? null))
            <div
                data-type="section-title"
                data-rte="true"
                @class([
                    'font-apex-book',
                    'mt-4' => filled($tab['tagline'] ?? null),
                ])
                style="
                    --ipa-color-light: oklch(0.3152 0.1176 262.41);
                    --ipa-color-dark: oklch(0.9011 0.0552 218.07);
                    color: var(--ipa-color-light);
                "
            >
                <h3 class="cms-section-title mb-0 text-left">
                    {{ $tab['title'] }}
                </h3>
            </div>
        @endif

        @if($hasContent)
            <div
                @class([
                    'cms-page-content cms-governance-content-block__body text-[color:var(--ipa-color)] text-xl font-din text-primary cms-rich-text__body',
                    'mt-8' => filled($tab['title'] ?? null) || filled($tab['tagline'] ?? null),
                ])
                data-type="section-description"
                data-rte="true"
                style="
                    --ipa-color-light: oklch(0.464 0 0);
                    --ipa-color-dark: oklch(0.9612 0 0);
                    color: var(--ipa-color-light);
                "
            >
                {!! $contentHtml !!}
            </div>
        @endif
    </div>

    @if(filled($tab['button_label'] ?? null) && filled($tab['url'] ?? null))
        <div class="pt-4 text-left">
            <x-cta-button
                :label="$tab['button_label']"
                :url="$tab['url']"
                style="secondary"
            />
        </div>
    @endif
</div>
