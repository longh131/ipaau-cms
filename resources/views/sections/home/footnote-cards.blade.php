@php
    /** @var array{items: array<int, array{title: string, url: ?string, image_desktop: ?string, image_mobile: ?string, show_arrow: bool}>} $footnoteCards */
    $footnoteCards = $footnoteCards ?? ['items' => []];
@endphp
@if(! empty($footnoteCards['items']))
        <section
          data-index="0.5"
          class=""
          style="
            --ipa-card-basis-sm: calc(100%);
            --ipa-card-basis-md: calc(100% / 2 - 2.5rem);
            --ipa-card-basis-lg: calc(100% / 6 - 2.5rem);
          "
        >
          <div data-type="footnote" class="inner mx-auto px-4 md:px-10">
            <div
              class="flex flex-wrap align-stretch gap-4 sm:gap-10 justify-center -mt-16"
            >
              @foreach ($footnoteCards['items'] as $card)
              <x-footnote-card
                :title="$card['title']"
                :url="$card['url']"
                :image-desktop="$card['image_desktop']"
                :image-mobile="$card['image_mobile']"
                :show-arrow="$card['show_arrow']"
              />
              @endforeach
            </div>
          </div>
        </section>
@endif
