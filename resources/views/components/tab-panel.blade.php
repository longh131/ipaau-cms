@props([
    'tab',
    'hidden' => false,
])

@php
    /** @var array{tab_label: string, tagline: string, title: string, description: string, button_label: string, url: ?string, image: ?string} $tab */
    $descriptionParagraphs = filled($tab['description'] ?? null)
        ? preg_split('/\R\R+/', trim($tab['description'])) ?: []
        : [];
@endphp

<div
    data-type="tab-content"
    @if(filled($tab['image'] ?? null)) data-tab-image="{{ $tab['image'] }}" @endif
    @class([
        'space-y-8 grow flex flex-col justify-center',
        'hidden' => $hidden,
    ])
>
    <div class="text-left container mx-auto">
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
            class="font-apex-book"
            style="
                --ipa-color-light: oklch(0.3152 0.1176 262.41);
                --ipa-color-dark: oklch(0.9011 0.0552 218.07);
                color: var(--ipa-color-light);
            "
        >
            <h3
                class="cms-section-title"
                style="text-align: left"
            >
                {{ $tab['title'] }}
            </h3>
        </div>
        @endif
        @if(! empty($descriptionParagraphs))
        <div
            class="text-[color:var(--ipa-color)] mt-8 text-xl font-din"
            data-type="section-description"
            data-rte="true"
            style="
                --ipa-color-light: oklch(0.464 0 0);
                --ipa-color-dark: oklch(0.9612 0 0);
                color: var(--ipa-color-light);
            "
        >
            @foreach ($descriptionParagraphs as $paragraph)
            @if(filled(trim($paragraph)))
            <div style="text-align: left">
                <span class="text-primary">{{ trim($paragraph) }}</span>
            </div>
            @endif
            @endforeach
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
