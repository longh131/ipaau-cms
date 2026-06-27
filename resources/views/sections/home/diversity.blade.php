@php
    /** @var array{title_html: string, image: ?string}|null $diversity */
    $diversity = $diversity ?? ['title_html' => '', 'image' => null];
    $hasContent = filled($diversity['title_html'] ?? null) || filled($diversity['image'] ?? null);
@endphp
@if($hasContent)
        <section
          data-type="diversity"
          data-index="9"
          class="diversity-section"
        >
          <div class="diversity-section__inner inner container px-4 md:px-10 mx-auto">
            @if(filled($diversity['image'] ?? null))
            <img
              loading="lazy"
              class="diversity-section__image"
              src="{{ $diversity['image'] }}"
              alt=""
            />
            @endif
            @if(filled($diversity['title_html'] ?? null))
            <div
              data-type="section-title"
              data-rte="true"
              class="diversity-section__title font-apex-book"
            >
              {!! $diversity['title_html'] !!}
            </div>
            @endif
          </div>
        </section>
@endif
