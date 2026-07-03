@php
    /** @var array{heading: string, slides: array<int, array{quote: string, author: string}>} $block */
    $slides = $block['slides'] ?? [];
    $total = count($slides);
@endphp

@if($total > 0)
    <section
        data-type="testimonialCarousel"
        class="cms-body-block cms-body-block--carousel testimonial-carousel-section py-12 overflow-hidden bg-[color:var(--bg-color)] relative"
        style="
            --bg-color: transparent;
            --ipa-color-light: oklch(0.464 0 0);
            --ipa-color-dark: oklch(1 0 0);
            --light-or-dark: light;
            color: var(--ipa-color-light);
        "
    >
        <div class="inner container px-4 md:px-10 mx-auto flex flex-col gap-12 relative z-[1]">
            @if(filled($block['heading'] ?? null))
                <div class="text-center about-rich-text">
                    <h4 class="text-display-lg lg:text-display-xl mb-0">
                        <span class="text-secondary text-warm-plum">{{ $block['heading'] }}</span>
                    </h4>
                </div>
            @endif

            <div class="testimonial-carousel testimonial-carousel--text-only">
                <div class="testimonial-carousel__stage">
                    <div class="swiper swiper-initialized swiper-horizontal">
                        <div class="swiper-wrapper" aria-live="polite">
                            @foreach ($slides as $index => $slide)
                                <x-cms-testimonial-slide
                                    :slide="$slide"
                                    :index="$index"
                                    :total="$total"
                                />
                            @endforeach
                        </div>

                        @if($total > 1)
                            <div class="swiper-pagination swiper-pagination-clickable swiper-pagination-bullets swiper-pagination-horizontal">
                                @foreach ($slides as $index => $slide)
                                    <span
                                        @class([
                                            'swiper-pagination-bullet',
                                            'swiper-pagination-bullet-active' => $index === 0,
                                        ])
                                        tabindex="0"
                                        role="button"
                                        aria-label="Go to slide {{ $index + 1 }}"
                                        @if($index === 0) aria-current="true" @endif
                                    ></span>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
@endif
