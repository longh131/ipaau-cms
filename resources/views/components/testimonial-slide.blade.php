@props([
    'item',
    'index' => 0,
    'total' => 1,
])

@php
    /** @var array{title: string, title_lines: array<int, string>, content: string, image: ?string} $item */
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

        <div
            class="testimonial-card__navigation testimonial-card__navigation--prev invisible group-[:is(.swiper-slide-active_&amp;)]/testimonial:visible"
        >
            <button
                type="button"
                class="testimonial-card__nav-button hover:scale-110 transform transition-transform duration-300 text-secondary"
                aria-label="Previous testimonial"
                title="Previous testimonial"
                aria-controls="testimonial-swiper"
            >
                <span aria-hidden="true" class="rotate-180 inline-block">
                    @include('partials.icons.testimonial-nav-arrow')
                </span>
                <span class="sr-only">Previous testimonial</span>
            </button>
        </div>

        <figure
            class="testimonial-card__figure testimonial-card__figure--split"
            itemscope=""
            itemtype="https://schema.org/Review"
        >
            <div class="testimonial-card__layout">
                <div class="testimonial-card__copy">
                    @if(filled($item['content'] ?? null))
                    <blockquote
                        itemprop="reviewBody"
                        class="testimonial-card__quote font-apex-book text-secondary text-xl md:text-display-xs xl:text-display-lg w-full max-w-full mx-auto"
                    >
                        {{ $item['content'] }}
                    </blockquote>
                    @endif
                    @if(! empty($item['title_lines']))
                    <div
                        class="testimonial-card__details"
                        itemprop="author"
                        itemscope=""
                        itemtype="https://schema.org/Person"
                    >
                        <div class="testimonial-card__meta text-md font-inter text-primary">
                            <span class="testimonial-card__title block" itemprop="jobTitle">
                                @foreach ($item['title_lines'] as $line)
                                @if(! $loop->first)<br />@endif{{ $line }}
                                @endforeach
                            </span>
                        </div>
                    </div>
                    @endif
                </div>
                @if(filled($item['image'] ?? null))
                <div class="testimonial-card__media">
                    <img
                        loading="lazy"
                        class="testimonial-card__portrait object-cover"
                        src="{{ $item['image'] }}"
                        alt=""
                        itemprop="image"
                    />
                </div>
                @endif
            </div>
        </figure>

        <div
            class="testimonial-card__navigation testimonial-card__navigation--next invisible group-[:is(.swiper-slide-active_&amp;)]/testimonial:visible"
        >
            <button
                type="button"
                class="testimonial-card__nav-button hover:scale-110 transform transition-transform duration-300 text-secondary"
                aria-label="Next testimonial"
                title="Next testimonial"
                aria-controls="testimonial-swiper"
            >
                <span aria-hidden="true">
                    @include('partials.icons.testimonial-nav-arrow')
                </span>
                <span class="sr-only">Next testimonial</span>
            </button>
        </div>
    </div>
</div>
