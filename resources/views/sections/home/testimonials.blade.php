@php
    /** @var array{items: array<int, array{title: string, title_lines: array<int, string>, content: string, image: ?string}>} $testimonials */
    $testimonials = $testimonials ?? ['items' => []];
    $items = $testimonials['items'] ?? [];
    $total = count($items);
@endphp
@if($total > 0)
        <section
          data-type="testimonialCarousel"
          data-index="6"
          class="testimonial-carousel-section py-12 overflow-hidden bg-[color:var(--bg-color)] relative"
          style="
            --bg-color: transparent;
            --ipa-color-light: oklch(0.464 0 0);
            --ipa-color-dark: oklch(1 0 0);
            --light-or-dark: light;
            color: var(--ipa-color-light);
          "
        >
          @include('partials.testimonials.carousel-halo')
          <div
            class="inner container px-4 md:px-10 mx-auto flex flex-col gap-12 relative z-[1]"
          >
            <div class="testimonial-carousel">
              <div class="testimonial-carousel__stage">
                <div class="swiper swiper-initialized swiper-horizontal swiper-backface-hidden">
                <div class="swiper-wrapper" aria-live="off">
                  @foreach ($items as $index => $item)
                  <x-testimonial-slide :item="$item" :index="$index" :total="$total" />
                  @endforeach
                </div>
                @if($total > 1)
                <div
                  class="swiper-pagination swiper-pagination-clickable swiper-pagination-bullets swiper-pagination-horizontal"
                >
                  @foreach ($items as $index => $item)
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
                <span class="swiper-notification" aria-live="assertive" aria-atomic="true"></span>
                </div>
              </div>
              <section class="flex justify-center mt-6" aria-label="更多会员风采">
                <a
                  href="{{ url('/category/member-stories-content') }}"
                  class="testimonial-card__navigation-autoplay-button inline-block no-underline"
                >
                  更多会员风采
                </a>
              </section>
            </div>
          </div>
        </section>
@endif
