@php
    /** @var array{
     *     tagline: string,
     *     title: string,
     *     intro: string,
     *     items: array<int, array{question: string, answer: string}>
     * } $block */
    $items = $block['items'] ?? [];
    $hasHeader = filled($block['tagline'] ?? null)
        || filled($block['title'] ?? null)
        || filled($block['intro'] ?? null);
@endphp

@if(! empty($items))
    <section
        data-type="accordion"
        class="cms-body-block cms-body-block--faq py-8 lg:py-12 centered bg-[color:var(--bg-color)]"
        style="
            --bg-color: #ffffff;
            --ipa-color-light: oklch(0.464 0 0);
            --ipa-color-dark: oklch(1 0 0);
            --light-or-dark: light;
            color: var(--ipa-color-light);
        "
    >
        <div class="inner container px-4 md:px-10 mx-auto px-7 flex flex-col gap-12">
            @if($hasHeader)
                <div class="text-center container mx-auto">
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
                        <h2
                            data-type="section-title"
                            class="font-apex-book text-display-2xl text-secondary mt-4 mb-0"
                        >{{ $block['title'] }}</h2>
                    @endif

                    @if(filled($block['intro'] ?? null))
                        <div
                            class="text-[color:var(--ipa-color)] mt-8 text-xl font-din text-primary"
                            data-type="section-description"
                        >{{ $block['intro'] }}</div>
                    @endif
                </div>
            @endif

            <ul @class(['border-b border-grey-subtle', 'mt-16' => $hasHeader])>
                @foreach ($items as $item)
                    <li class="border-t border-grey-subtle">
                        <button
                            type="button"
                            class="flex justify-between items-center text-left w-full pt-6 pb-6"
                            aria-expanded="false"
                        >
                            <span class="text-secondary text-xl font-medium">{{ $item['question'] }}</span>
                            <div class="ml-2 circle rounded-xl border-2 border-secondary text-warm-plum">
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke-width="1.5"
                                    stroke="currentColor"
                                    aria-hidden="true"
                                    class="w-4 h-4 shrink-0"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M12 4.5v15m7.5-7.5h-15"
                                    ></path>
                                </svg>
                                <span class="sr-only">Open Accordion</span>
                            </div>
                        </button>
                        @if(filled($item['answer'] ?? null))
                            <div data-rte="true" class="mb-6 hidden">
                                <div>{!! $item['answer'] !!}</div>
                            </div>
                        @endif
                    </li>
                @endforeach
            </ul>
        </div>
    </section>
@endif
