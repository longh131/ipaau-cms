@php
    /** @var array{tabs: array<int, array{
     *     tab_label: string,
     *     tagline: string,
     *     title: string,
     *     content_html: string,
     *     button_label: string,
     *     url: ?string
     * }>} $block */
    $tabs = $block['tabs'] ?? [];
@endphp

@if(! empty($tabs))
    <section
        data-type="tabbedContent"
        class="py-12 cms-governance-module cms-general-secondary-module cms-tabbed-content--no-image bg-[color:var(--bg-color)]"
        style="
            --bg-color: transparent;
            --ipa-color-light: oklch(0.464 0 0);
            --ipa-color-dark: oklch(1 0 0);
            --light-or-dark: light;
            color: var(--ipa-color-light);
        "
    >
        <div class="inner container px-4 md:px-10 mx-auto flex flex-col gap-12">
            <div class="container w-full">
                <div class="lg:hidden">
                    <div class="cms-tabbed-content__tab-list flex flex-wrap gap-4 xl:mb-auto mt-8 shrink grow-0">
                        @foreach ($tabs as $index => $tab)
                            <x-tab-button :label="$tab['tab_label']" :active="$index === 0" />
                        @endforeach
                    </div>
                </div>

                <div class="flex h-full flex-col items-start w-full">
                    <div class="hidden lg:block w-full">
                        <div class="cms-tabbed-content__tab-list flex flex-wrap gap-4 xl:mb-auto mt-8 shrink grow-0">
                            @foreach ($tabs as $index => $tab)
                                <x-tab-button :label="$tab['tab_label']" :active="$index === 0" />
                            @endforeach
                        </div>
                    </div>

                    @foreach ($tabs as $index => $tab)
                        @include('frontend.pages.partials.body-blocks.partials.tab-panel-rich', [
                            'tab' => $tab,
                            'hidden' => $index > 0,
                        ])
                    @endforeach
                </div>
            </div>
        </div>
    </section>
@endif
