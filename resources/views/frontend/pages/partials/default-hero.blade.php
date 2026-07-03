@if($pageView['has_hero'] ?? false)
    <section
        data-type="pageHero"
        class="py-12 overflow-hidden about-section bg-[color:var(--bg-color)]"
        style="
            --bg-color: transparent;
            --ipa-color-light: oklch(0.464 0 0);
            --ipa-color-dark: oklch(1 0 0);
            --light-or-dark: light;
            color: var(--ipa-color-light);
        "
    >
        <div class="inner container px-4 md:px-10 mx-auto flex flex-col gap-12">
            <div class="container mx-auto px-4 pt-28 pb-12">
                <div class="grid grid-cols-1 lg:grid-cols-[40%_1fr] items-center gap-14 lg:gap-20 about-page-hero">
                    @if(filled($pageView['hero_image'] ?? null))
                        <div class="content-section content-section-1 content-section-1--image row-start-1 col-start-1">
                            <div class="about-cta__image about-cta__image--acorn">
                                <img
                                    src="{{ $pageView['hero_image'] }}"
                                    alt="{{ $pageView['hero_title'] ?? '' }}"
                                    loading="lazy"
                                />
                            </div>
                        </div>
                    @endif
                    <div @class([
                        'content-section content-section-2',
                        'lg:row-start-1 lg:col-start-2' => filled($pageView['hero_image'] ?? null),
                    ])>
                        <div class="text-left container mx-auto about-rich-text">
                            <h1 class="text-display-2xl lg:text-display-3xl md:text-display-3xl lg:text-display-4xl">
                                <span class="text-secondary">{{ $pageView['hero_title'] ?? $page->displayTitle() }}</span>
                            </h1>
                            @if(filled($pageView['hero_intro_html'] ?? null))
                                <div
                                    class="text-[color:var(--ipa-color)] mt-8 text-2xl font-din"
                                    data-type="section-description"
                                    data-rte="true"
                                >
                                    {!! $pageView['hero_intro_html'] !!}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endif
