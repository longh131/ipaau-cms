@props([
    'slide' => [],
    'index' => 0,
    'total' => 1,
])

@php
    /** @var array{quote: string, author: string} $slide */
    $quote = trim((string) ($slide['quote'] ?? ''));
    $author = trim((string) ($slide['author'] ?? ''));
@endphp

<div
    @class([
        'swiper-slide',
        'swiper-slide-active' => $index === 0,
        'swiper-slide-prev' => $total > 1 && $index === $total - 1,
        'swiper-slide-next' => $total > 1 && $index === 1,
    ])
    role="group"
    aria-label="{{ $index + 1 }} / {{ $total }}"
>
    <div @class(['testimonial-card__wrapper group/testimonial', 'testimonial-card_single' => $total === 1])>
        @if($total > 1)
            <div class="testimonial-card__navigation testimonial-card__navigation--prev invisible group-[:is(.swiper-slide-active_&amp;)]/testimonial:visible">
                <button
                    type="button"
                    class="testimonial-card__nav-button hover:scale-110 transform transition-transform duration-300 text-secondary"
                    aria-label="Previous testimonial"
                    title="Previous testimonial"
                >
                    <span aria-hidden="true" class="rotate-180 inline-block">
                        @include('partials.icons.testimonial-nav-arrow')
                    </span>
                    <span class="sr-only">Previous testimonial</span>
                </button>
            </div>
        @endif

        <figure
            class="testimonial-card__figure testimonial-card__figure--split testimonial-card__figure--text-only"
            itemscope
            itemtype="https://schema.org/Review"
        >
            <div class="testimonial-card__layout">
                <div class="testimonial-card__copy">
                    @if(filled($quote))
                        <blockquote
                            itemprop="reviewBody"
                            class="testimonial-card__quote font-apex-book text-secondary text-xl md:text-display-xs xl:text-display-lg w-full max-w-full mx-auto"
                        >
                            {{ $quote }}
                        </blockquote>
                    @endif

                    @if(filled($author))
                        <div
                            class="testimonial-card__details"
                            itemprop="author"
                            itemscope
                            itemtype="https://schema.org/Person"
                        >
                            <div class="testimonial-card__meta text-md font-inter text-primary">
                                <strong class="testimonial-card__name font-semibold text-lg" itemprop="name">
                                    {{ $author }}
                                </strong>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </figure>

        @if($total > 1)
            <div class="testimonial-card__navigation testimonial-card__navigation--next invisible group-[:is(.swiper-slide-active_&amp;)]/testimonial:visible">
                <button
                    type="button"
                    class="testimonial-card__nav-button hover:scale-110 transform transition-transform duration-300 text-secondary"
                    aria-label="Next testimonial"
                    title="Next testimonial"
                >
                    <span aria-hidden="true">
                        @include('partials.icons.testimonial-nav-arrow')
                    </span>
                    <span class="sr-only">Next testimonial</span>
                </button>
            </div>
        @endif
    </div>
</div>
