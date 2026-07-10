@php
    /** @var array{tagline: string, title_lines: array<int, string>, description: string, buttons: array<int, array{label: string, url: string, target: ?string, style: string>}|null $aboutIntro */
    $aboutIntro = $aboutIntro ?? ['tagline' => '', 'title_lines' => [], 'description' => '', 'buttons' => []];
    $hasContent = filled($aboutIntro['tagline'] ?? null)
        || ! empty($aboutIntro['title_lines'])
        || filled($aboutIntro['description'] ?? null)
        || ! empty($aboutIntro['buttons']);
    $descriptionParagraphs = filled($aboutIntro['description'] ?? null)
        ? array_values(array_filter(array_map(
            'trim',
            preg_split("/\n{2,}/", str_replace(["\r\n", "\r"], "\n", trim($aboutIntro['description']))) ?: []
        ), fn (string $paragraph) => $paragraph !== ''))
        : [];
@endphp
@if($hasContent)
        <section
          data-index="8"
          class="py-12 text-left bg-[color:var(--bg-color)]"
          style="
            --bg-color: #ffffff;
            --ipa-color-light: oklch(0.464 0 0);
            --ipa-color-dark: oklch(1 0 0);
            --light-or-dark: light;
            color: var(--ipa-color-light);
          "
        >
          <div
            class="inner container px-4 md:px-10 mx-auto flex flex-col gap-12"
          >
            <div class="text-left container mx-auto">
              @if(filled($aboutIntro['tagline'] ?? null))
              <span
                class="eyebrow-xl"
                style="
                  --ipa-color-light: oklch(0.4867 0.1803 336.11);
                  --ipa-color-dark: oklch(0.8944 0.0357 331.62);
                  color: var(--ipa-color-light);
                "
                >{{ $aboutIntro['tagline'] }}</span
              >
              @endif
              @if(! empty($aboutIntro['title_lines']))
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
                @foreach ($aboutIntro['title_lines'] as $line)
                <h3
                  class="cms-section-title"
                  style="text-align: left"
                >
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
                <div style="text-align: left">
                  <p>{{ $paragraph }}</p>
                </div>
                @endforeach
              </div>
              @endif
            </div>
            <div>
              @if(! empty($aboutIntro['buttons']))
              <div class="flex flex-col sm:flex-row gap-4 justify-left">
                @foreach ($aboutIntro['buttons'] as $button)
                <x-cta-button
                  :label="$button['label']"
                  :url="$button['url']"
                  :style="$button['style']"
                  :target="$button['target']"
                />
                @endforeach
              </div>
              @endif
              <picture
                ><source
                  srcset="{{ asset('new/assets/greg-rosenke-gQL0DUdciGQ-unsplash.jpg') }}"
                  media="screen and (min-width: 768px)"
                  alt="" />
                <img
                  loading="lazy"
                  class="mt-10 sm:mt-20 rounded-3xl inline-block"
                  src="{{ asset('new/assets/108-400x400.webp') }}"
                  alt=""
              /></picture>
            </div>
          </div>
        </section>
@endif
