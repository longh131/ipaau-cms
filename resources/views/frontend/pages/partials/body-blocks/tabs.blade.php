@php
    /** @var array{tabs: array<int, array{tab_label: string, tagline: string, title: string, description: string, button_label: string, url: ?string, image: ?string}>} $block */
    $tabs = $block['tabs'] ?? [];
    $firstImage = $tabs[0]['image'] ?? null;
@endphp

@if(! empty($tabs))
    <section
        data-type="tabbedContent"
        class="cms-body-block cms-body-block--tabs py-8 lg:py-12 bg-[color:var(--bg-color)]"
        style="
            --bg-color: transparent;
            --ipa-color-light: oklch(0.464 0 0);
            --ipa-color-dark: oklch(1 0 0);
            --light-or-dark: light;
            color: var(--ipa-color-light);
        "
    >
        <div class="inner container px-4 md:px-10 mx-auto flex flex-col gap-12">
            <div class="container">
                <div class="grid grid-cols-1 gap-8 lg:gap-16 items-center lg:grid-cols-2">
                    <div class="lg:hidden">
                        <div class="flex flex-wrap gap-4 mb-8 xl:mb-auto mt-8 shrink grow-0">
                            @foreach ($tabs as $index => $tab)
                                <x-tab-button :label="$tab['tab_label']" :active="$index === 0" />
                            @endforeach
                        </div>
                    </div>
                    <div class="flex h-full flex-col items-start">
                        <div class="hidden lg:block">
                            <div class="flex flex-wrap gap-4 mb-8 xl:mb-auto mt-8 shrink grow-0">
                                @foreach ($tabs as $index => $tab)
                                    <x-tab-button :label="$tab['tab_label']" :active="$index === 0" />
                                @endforeach
                            </div>
                        </div>
                        @foreach ($tabs as $index => $tab)
                            <x-tab-panel :tab="$tab" :hidden="$index > 0" />
                        @endforeach
                    </div>
                    <div>
                        <div class="img-shape-acorn img-wrapper">
                            @if(filled($firstImage))
                                <img
                                    src="{{ $firstImage }}"
                                    alt=""
                                    loading="lazy"
                                />
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endif
