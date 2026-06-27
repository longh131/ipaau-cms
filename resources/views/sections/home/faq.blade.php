@php
    /** @var array{items: array<int, array{question: string, answer: string}>} $faq */
    $faq = $faq ?? ['items' => []];
    $items = $faq['items'] ?? [];
@endphp
@if(! empty($items))
        <section
          data-type="accordion"
          data-index="12"
          class="py-12 centered bg-[color:var(--bg-color)]"
          style="
            --bg-color: #ffffff;
            --ipa-color-light: oklch(0.464 0 0);
            --ipa-color-dark: oklch(1 0 0);
            --light-or-dark: light;
            color: var(--ipa-color-light);
          "
        >
          <div
            class="inner container px-4 md:px-10 mx-auto px-7 flex flex-col gap-12"
          >
            <div class="text-center text-center container mx-auto">
              <span
                class="eyebrow-xl"
                style="
                  --ipa-color-light: oklch(0.4867 0.1803 336.11);
                  --ipa-color-dark: oklch(0.8944 0.0357 331.62);
                  color: var(--ipa-color-light);
                "
                >HAVE A QUESTION?</span
              >
              <h1
                data-type="section-title"
                data-rte="true"
                class="font-apex-book"
                style="
                  --ipa-color-light: oklch(0.3152 0.1176 262.41);
                  --ipa-color-dark: oklch(0.9011 0.0552 218.07);
                  color: var(--ipa-color-light);
                "
              >
                <div style="text-align: center">
                  <span class="text-display-2xl text-secondary"
                    >Frequently Asked Questions</span
                  >
                </div>
              </h1>
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
                <div style="text-align: center">
                  <span class="text-xl text-primary"
                    >Have questions? We've got answers&nbsp;<br />Learn more
                    below</span
                  >
                </div>
              </div>
            </div>
            <ul class="border-b border-grey-subtle mt-16">
              @foreach ($items as $item)
              <li class="border-t border-grey-subtle">
                <button
                  type="button"
                  class="flex justify-between items-center text-left w-full pt-6 pb-6"
                  aria-expanded="false"
                >
                  <span class="text-secondary text-xl font-medium"
                    >{{ $item['question'] }}</span
                  >
                  <div
                    class="ml-2 circle rounded-xl border-2 border-secondary text-warm-plum"
                  >
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      fill="none"
                      viewBox="0 0 24 24"
                      stroke-width="1.5"
                      stroke="currentColor"
                      aria-hidden="true"
                      data-slot="icon"
                      role="none"
                      class="w-4 h-4 shrink-0"
                    >
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M12 4.5v15m7.5-7.5h-15"
                      ></path></svg
                    ><span class="sr-only">Open Accordion</span>
                  </div>
                </button>
                @if(filled($item['answer'] ?? null))
                <div data-rte="true" class="mb-6 hidden">
                  <div>{!! $item['answer'] !!}</div>
                </div>
                @endif
              </li>
              @endforeach
            </ul>
          </div>
        </section>
@endif
