@php
    /** @var array{tagline: string, title_lines: array<int, string>, description: string, buttons: array<int, array{label: string, url: string, target: ?string, style: string}>, image: ?string}|null $ctaSection */
    $ctaSection = $ctaSection ?? ['tagline' => '', 'title_lines' => [], 'description' => '', 'buttons' => [], 'image' => null];
    $hasContent = filled($ctaSection['tagline'] ?? null)
        || ! empty($ctaSection['title_lines'])
        || filled($ctaSection['description'] ?? null)
        || ! empty($ctaSection['buttons'])
        || filled($ctaSection['image'] ?? null);
    $descriptionParagraphs = filled($ctaSection['description'] ?? null)
        ? array_values(array_filter(array_map(
            'trim',
            preg_split("/\n{2,}/", str_replace(["\r\n", "\r"], "\n", trim($ctaSection['description']))) ?: []
        ), fn (string $paragraph) => $paragraph !== ''))
        : [];
@endphp
@if($hasContent)
        <section
          data-type="ctaSection"
          data-index="10"
          class="py-12 overflow-hidden bg-[color:var(--bg-color)]"
          style="
            --bg-color: transparent;
            --ipa-color-light: oklch(0.464 0 0);
            --ipa-color-dark: oklch(1 0 0);
            --light-or-dark: light;
            color: var(--ipa-color-light);
          "
        >
          <div
            class="inner container px-4 md:px-10 mx-auto flex flex-col gap-12"
          >
            <div class="container mx-auto px-4 py-16 lg:py-20">
              <div
                class="grid grid-cols-1 lg:grid-cols-[40%_1fr] items-center gap-14 lg:gap-20"
              >
                @if(filled($ctaSection['image'] ?? null))
                <div
                  class="content-section content-section-1 content-section-1--image row-start-1 col-start-1"
                >
                  <div
                    class="img-shape-rectangle img-wrapper"
                    style="--cta-bg-desktop: url('{{ $ctaSection['image'] }}'); --cta-bg-mobile: url('{{ $ctaSection['image'] }}')"
                  ></div>
                </div>
                @endif
                <div
                  @class([
                      'content-section content-section-2',
                      'lg:row-start-1 lg:col-start-2' => filled($ctaSection['image'] ?? null),
                  ])
                >
                  <div class="text-left container mx-auto">
                    @if(filled($ctaSection['tagline'] ?? null))
                    <span
                      class="eyebrow-xl"
                      style="
                        --ipa-color-light: oklch(0.4867 0.1803 336.11);
                        --ipa-color-dark: oklch(0.8944 0.0357 331.62);
                        color: var(--ipa-color-light);
                      "
                      >{{ $ctaSection['tagline'] }}</span
                    >
                    @endif
                    @if(! empty($ctaSection['title_lines']))
                    <div
                      data-type="section-title"
                      data-rte="true"
                      class="font-apex-book"
                      style="
                        --ipa-color-light: oklch(0.3152 0.1176 262.41);
                        --ipa-color-dark: oklch(0.9011 0.0552 218.07);
                        color: var(--ipa-color-light);
                      "
                    >
                      @foreach ($ctaSection['title_lines'] as $line)
                      <h3 class="text-display-lg lg:text-display-xl">
                        {{ $line }}
                      </h3>
                      @endforeach
                    </div>
                    @endif
                    @if(! empty($descriptionParagraphs))
                    <div
                      class="text-[color:var(--ipa-color)] mt-8 text-xl font-din"
                      data-type="section-description"
                      data-rte="true"
                      style="
                        --ipa-color-light: oklch(0.464 0 0);
                        --ipa-color-dark: oklch(0.9612 0 0);
                        color: var(--ipa-color-light);
                      "
                    >
                      @foreach ($descriptionParagraphs as $paragraph)
                      <p>{{ $paragraph }}</p>
                      @endforeach
                    </div>
                    @endif
                  </div>
                  @if(! empty($ctaSection['buttons']))
                  <div
                    class="component-cta flex flex-col shrink-0 sm:flex-row gap-4 lg:justify-start"
                  >
                    @foreach ($ctaSection['buttons'] as $button)
                    <x-cta-button
                      :label="$button['label']"
                      :url="$button['url']"
                      :style="$button['style']"
                      :target="$button['target']"
                    />
                    @endforeach
                  </div>
                  @endif
                </div>
              </div>
            </div>
          </div>
        </section>
@endif
