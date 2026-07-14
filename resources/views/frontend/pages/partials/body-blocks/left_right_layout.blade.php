@php
    /** @var array{
     *     tagline: string,
     *     title: string,
     *     title_gradient_class: string,
     *     content_html: string,
     *     buttons: array<int, array{label: string, url: string, style: string, target: string}>
     * } $block */
    $tagline = trim((string) ($block['tagline'] ?? ''));
    $title = trim((string) ($block['title'] ?? ''));
    $titleGradientClass = trim((string) ($block['title_gradient_class'] ?? 'text-gradient-purple-reverse'));
    $contentHtml = (string) ($block['content_html'] ?? '');
    $buttons = $block['buttons'] ?? [];

    $hasLeft = filled($tagline) || filled($title);
    $hasRight = filled(strip_tags($contentHtml)) || $buttons !== [];
@endphp

@if($hasLeft || $hasRight)
    <section
        data-type="basicContentWithColumns"
        class="py-12 cms-governance-module cms-general-secondary-module cms-left-right-layout bg-[color:var(--bg-color)]"
        style="
            --bg-color: transparent;
            --ipa-color-light: oklch(0.464 0 0);
            --ipa-color-dark: oklch(1 0 0);
            --light-or-dark: light;
            color: var(--ipa-color-light);
        "
    >
        <div class="inner container px-4 md:px-10 mx-auto px-7 flex flex-col gap-12">
            <div
                class="column-wrapper grid items-start text-left grid-cols-1 lg:grid-cols-2 [&>div:first-child]:border-l-0 [&>div:first-child]:border-t-0 [&>div]:max-lg:border-l-0 [&>*:nth-child(3n+3)]:lg:!border-l-0 [&>div:nth-child(2)]:xl:border-t-0 [&>*:nth-child(2n-3)]:lg:pr-5 [&>*:nth-child(2n-2)]:lg:pl-5 [&>*:nth-child(n+3)]:pt-5 [&>div]:border-[color:var(--ipa-border-color)]"
                style="--ipa-border-color: transparent; --ipa-border-style: solid"
            >
                @if($hasLeft)
                    <div class="max-lg:pt-5 first:max-lg:pt-0 column flex flex-col h-full pb-5 justify-start border-solid">
                        <div class="text-left container mx-auto">
                            @if(filled($title))
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
                                    <h2 class="max-w-prose cms-section-title text-display-xl lg:text-display-2xl mb-0 text-left">
                                        <span class="{{ $titleGradientClass }}">{{ $title }}</span>
                                    </h2>
                                </div>
                            @endif

                            @if(filled($tagline))
                                <p @class([
                                    'cms-left-right-layout__tagline max-w-prose text-display-xl lg:text-display-2xl mb-0 text-left font-normal',
                                    'mt-4' => filled($title),
                                ])>{{ $tagline }}</p>
                            @endif
                        </div>
                    </div>
                @endif

                @if($hasRight)
                    <div class="max-lg:pt-5 first:max-lg:pt-0 column flex flex-col h-full pb-5 justify-start border-solid">
                        @if(filled(strip_tags($contentHtml)))
                            <div class="text-left container mx-auto">
                                <div
                                    class="cms-page-content cms-governance-content-block__body text-[color:var(--ipa-color)] text-xl font-din text-primary cms-rich-text__body"
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
                            </div>
                        @endif

                        @if($buttons !== [])
                            <div class="basis-auto mt-12 flex flex-col sm:flex-row justify-start flex-wrap gap-6 mb-6">
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
            </div>
        </div>
    </section>
@endif
