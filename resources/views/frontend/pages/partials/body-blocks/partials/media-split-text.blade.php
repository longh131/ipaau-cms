@if($hasText ?? false)
    <div @class([
        'content-section content-section-2',
        'lg:row-start-1 lg:col-start-2' => ($imageLeft ?? true) && ($hasImage ?? false),
        'lg:row-start-1 lg:col-start-1' => ! ($imageLeft ?? true) && ($hasImage ?? false),
    ])>
        <div class="text-left about-rich-text">
            @if(filled($block['tagline'] ?? null))
                <span
                    class="eyebrow-xl"
                    style="
                        --ipa-color-light: oklch(0.4867 0.1803 336.11);
                        --ipa-color-dark: oklch(0.8944 0.0357 331.62);
                        color: var(--ipa-color-light);
                    "
                >{{ $block['tagline'] }}</span>
            @endif

            @if(filled($block['title'] ?? null))
                <h3 class="text-display-xl lg:text-display-2xl">
                    <span class="text-secondary">{{ $block['title'] }}</span>
                </h3>
            @endif

            @if(filled(strip_tags((string) ($block['content_html'] ?? ''))))
                <div
                    class="text-[color:var(--ipa-color)] mt-8 text-xl font-din cms-page-content"
                    data-type="section-description"
                    data-rte="true"
                >
                    {!! $block['content_html'] !!}
                </div>
            @endif

            @if(! empty($block['buttons'] ?? []))
                <div class="component-cta flex flex-col shrink-0 sm:flex-row flex-wrap gap-4 mt-8 lg:justify-start">
                    @foreach ($block['buttons'] as $button)
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
    </div>
@endif
