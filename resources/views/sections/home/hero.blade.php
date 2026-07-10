@php
    /** @var array{tagline: string, title_lines: array<int, string>, description: string, buttons: array<int, array{label: string, url: string, target: ?string, style: string>}|null $hero */
    $hero = $hero ?? ['tagline' => '', 'title_lines' => [], 'description' => '', 'buttons' => []];
@endphp
<section
          data-type="heroBanner"
          data-index="0"
          class="bg-[color:var(--bg-color)]"
          style="
            --bg-color: transparent;
            --ipa-color-light: oklch(0.464 0 0);
            --ipa-color-dark: oklch(1 0 0);
            --light-or-dark: light;
            color: var(--ipa-color-light);
          "
        >
          <div
            class="inner container px-4 md:px-10 mx-auto flex justify-center pt-28 pb-32 flex flex-col gap-12"
          >
            <div
              class="heroForeground max-w-full flex justify-center items-center gap-8"
            >
              <div class="basis-full max-w-full shrink-0">
                <div class="text-center container mx-auto">
                  @if(filled($hero['tagline'] ?? null))
                  <span
                    class="eyebrow-md"
                    style="
                      --ipa-color-light: oklch(0.4867 0.1803 336.11);
                      --ipa-color-dark: oklch(0.8944 0.0357 331.62);
                      color: var(--ipa-color-light);
                    "
                    >{{ $hero['tagline'] }}</span
                  >
                  @endif
                  @if(! empty($hero['title_lines']))
                  <div
                    data-type="hero-title"
                    data-rte="true"
                    class="font-apex-book"
                    style="
                      --ipa-color-light: oklch(0.3152 0.1176 262.41);
                      --ipa-color-dark: oklch(0.9011 0.0552 218.07);
                      color: var(--ipa-color-light);
                    "
                  >
                    @foreach ($hero['title_lines'] as $line)
                    <h1
                      class="text-display-2xl md:text-display-3xl lg:text-display-4xl"
                      style="text-align: center"
                    >
                      {{ $line }}
                    </h1>
                    @endforeach
                  </div>
                  @endif
                  @if(filled($hero['description'] ?? null))
                  <div
                    class="text-[color:var(--ipa-color)] mt-8 text-2xl font-din"
                    data-type="section-description"
                    data-rte="true"
                    style="
                      --ipa-color-light: oklch(0.464 0 0);
                      --ipa-color-dark: oklch(0.9612 0 0);
                      color: var(--ipa-color-light);
                    "
                  >
                    <div style="text-align: center">
                      {{ $hero['description'] }}
                    </div>
                  </div>
                  @endif
                </div>
                @if(! empty($hero['buttons']))
                <div
                  class="basis-full flex flex-col sm:flex-row justify-center flex-wrap gap-6 mt-12 mb-6"
                >
                  @foreach ($hero['buttons'] as $button)
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
        </section>
