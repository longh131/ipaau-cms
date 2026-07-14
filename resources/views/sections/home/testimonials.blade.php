@php
    /** @var array{
     *     section_title: string,
     *     items: array<int, array{title: string, title_lines: array<int, string>, content: string, image: ?string}>
     * } $testimonials */
    $testimonials = $testimonials ?? ['section_title' => '', 'items' => []];
    $sectionTitle = trim((string) ($testimonials['section_title'] ?? ''));
    $items = $testimonials['items'] ?? [];
    $total = count($items);
    $plainBackground = $plainBackground ?? false;
@endphp
@if($total > 0 || filled($sectionTitle))
        <section
          data-type="testimonialCarousel"
          data-index="6"
          @class([
            'testimonial-carousel-section py-12 overflow-hidden bg-[color:var(--bg-color)] relative',
            'testimonial-carousel-section--plain' => $plainBackground,
          ])
          style="
            --bg-color: transparent;
            --ipa-color-light: oklch(0.464 0 0);
            --ipa-color-dark: oklch(1 0 0);
            --light-or-dark: light;
            color: var(--ipa-color-light);
          "
        >
          @unless($plainBackground)
            @include('partials.testimonials.carousel-halo')
          @endunless
          <div
            class="inner container px-4 md:px-10 mx-auto flex flex-col gap-12 relative z-[1]"
          >
            @if(filled($sectionTitle))
                <div class="w-full text-center">
                    <h3 class="cms-section-title leading-tight tracking-[-.0253334em] mb-0 text-secondary">
                        {{ $sectionTitle }}
                    </h3>
                </div>
            @endif

            @if($total > 0)
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
              @if($total > 1)
              <section class="flex justify-center mt-6" aria-label="轮播控制">
                <button
                  type="button"
                  class="testimonial-card__navigation-autoplay-button"
                  aria-pressed="true"
                  aria-label="点击暂停"
                  title="点击暂停"
                >
                  <span aria-hidden="true">点击暂停</span>
                  <span class="sr-only">点击暂停</span>
                </button>
                <span
                  class="sr-only"
                  aria-live="polite"
                  aria-atomic="true"
                  style="position: absolute; left: -9999px"
                >轮播进行中</span>
              </section>
              @endif
            </div>
            @endif
          </div>
        </section>
@endif
