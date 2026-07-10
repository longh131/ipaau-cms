@php
    /** @var array{tagline: string, title_lines: array<int, string>, description: string, buttons: array<int, array{label: string, url: string, target: ?string, style: string>}|null $membership */
    $membership = $membership ?? ['tagline' => '', 'title_lines' => [], 'description' => '', 'buttons' => []];
    $hasLeft = filled($membership['tagline'] ?? null) || ! empty($membership['title_lines']);
    $hasRight = filled($membership['description'] ?? null) || ! empty($membership['buttons']);
    $descriptionParagraphs = filled($membership['description'] ?? null)
        ? preg_split('/\R\R+/', trim($membership['description'])) ?: []
        : [];
@endphp
@if($hasLeft || $hasRight)
        <section
          data-type="basicContentWithColumns"
          data-index="1"
          class="py-32 mx-auto rounded container bg-[color:var(--bg-color)]"
          style="
            --bg-color: transparent;
            --ipa-color-light: oklch(0.464 0 0);
            --ipa-color-dark: oklch(1 0 0);
            --light-or-dark: light;
            color: var(--ipa-color-light);
          "
        >
          <div
            class="inner container px-4 md:px-10 mx-auto px-7 flex flex-col gap-12"
          >
            <div
              class="column-wrapper grid items-stretch text-left grid-cols-1 lg:grid-cols-2 [&amp;&gt;div:first-child]:border-l-0 [&amp;&gt;div:first-child]:border-t-0 [&amp;&gt;div]:max-lg:border-l-0 [&amp;&gt;*:nth-child(3n+3)]:lg:!border-l-0 [&amp;&gt;div:nth-child(2)]:xl:border-t-0 [&amp;&gt;*:nth-child(2n-3)]:lg:pr-5 [&amp;&gt;*:nth-child(2n-2)]:lg:pl-5 [&amp;&gt;*:nth-child(n+3)]:pt-5 [&amp;&gt;div]:border-[color:var(--ipa-border-color)]"
              data-bw=""
              style="--ipa-border-color: transparent; --ipa-border-style: solid"
            >
              @if($hasLeft)
              <div
                class="max-lg:pt-5 first:max-lg:pt-0 column flex flex-col h-full pb-5 border-solid"
              >
                <div class="text-left container mx-auto">
                  @if(filled($membership['tagline'] ?? null))
                  <span
                    class="eyebrow-xl"
                    style="
                      --ipa-color-light: oklch(0.4867 0.1803 336.11);
                      --ipa-color-dark: oklch(0.8944 0.0357 331.62);
                      color: var(--ipa-color-light);
                    "
                    >{{ $membership['tagline'] }}</span
                  >
                  @endif
                  @if(! empty($membership['title_lines']))
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
                    @foreach ($membership['title_lines'] as $line)
                    <h3
                      class="cms-section-title"
                      style="text-align: left"
                    >
                      {{ $line }}
                    </h3>
                    @endforeach
                  </div>
                  @endif
                </div>
              </div>
              @endif
              @if($hasRight)
              <div
                class="max-lg:pt-5 first:max-lg:pt-0 column flex flex-col h-full pb-5 border-solid"
              >
                @if(! empty($descriptionParagraphs))
                <div class="text-left container mx-auto">
                  <div
                    class="text-[color:var(--ipa-color)] text-xl font-din"
                    data-type="section-description"
                    data-rte="true"
                    style="
                      --ipa-color-light: oklch(0.464 0 0);
                      --ipa-color-dark: oklch(0.9612 0 0);
                      color: var(--ipa-color-light);
                    "
                  >
                    @foreach ($descriptionParagraphs as $paragraph)
                    @if(filled(trim($paragraph)))
                    <div style="text-align: left">
                      <span class="text-primary">{{ trim($paragraph) }}</span>
                    </div>
                    @endif
                    @endforeach
                  </div>
                </div>
                @endif
                @if(! empty($membership['buttons']))
                <div
                  class="basis-auto mt-12 flex flex-col sm:flex-row justify-start flex-wrap gap-6 mt-12 mb-6"
                >
                  @foreach ($membership['buttons'] as $button)
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
              @endif
            </div>
          </div>
        </section>
@endif
