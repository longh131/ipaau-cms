<footer
        class="footer-main w-full bg-[color:var(--bg-color)] text-dark"
        style="--bg-color: oklch(1 0 0); --light-or-dark: dark"
      >
        <div class="container mx-auto py-16">
          <div
            class="grid grid-cols-4 md:grid-cols-8 xl:grid-cols-12 gap-x-8 gap-y-2"
          >
            <div
              id="footer-row-1"
              class="footer-row footer-row-1 col-span-4 md:col-span-8 xl:col-span-4 p-4 order-1 md:order-2 xl:order-1"
            >
              <div id="footer-logo">
                <img
                  src="{{ asset('assets/img/header-logo.png') }}"
                  alt="IPA Logo"
                  class="w-[86px] h-[70px]"
                />
              </div>
              <div id="footer-disclaimer" class="pt-2">
                <div data-rte="true">
                  {!! $footerDisclaimer ?? '' !!}
                </div>
              </div>
              @if (! empty($footerSocialLinks))
              <ul class="footer-social-media-links">
                @foreach ($footerSocialLinks as $social)
                <li>
                  @if (($social['type'] ?? 'link') === 'qrcode')
                  <span
                    class="footer-social-qrcode inline-block transition-all duration-500 {{ filled($social['qrcode']) ? 'cursor-pointer' : 'opacity-60' }}"
                    title="{{ filled($social['qrcode']) ? $social['label'] : $social['label'].'（请在系统设置中上传二维码）' }}"
                    tabindex="{{ filled($social['qrcode']) ? '0' : '-1' }}"
                  >
                    <img
                      class="w-[24px] h-[24px] transition-all duration-500 {{ filled($social['qrcode']) ? 'group-hover:scale-[1.1]' : '' }}"
                      alt="{{ $social['label'] }}"
                      src="{{ $social['icon'] }}"
                    />
                    @if (filled($social['qrcode']))
                    <span class="footer-social-qrcode-popup" role="tooltip">
                      <img src="{{ $social['qrcode'] }}" alt="{{ $social['label'] }}二维码" />
                    </span>
                    @endif
                    <span class="sr-only">{{ $social['label'] }}</span>
                  </span>
                  @elseif (filled($social['url']))
                  <a
                    href="{{ $social['url'] }}"
                    title="{{ $social['label'] }}"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="block w-[24px] transition-all duration-500 border-transparent group"
                  >
                    <img
                      class="w-full h-auto group-hover:scale-[1.1] transition-all duration-500"
                      alt="{{ $social['label'] }}"
                      src="{{ $social['icon'] }}"
                    />
                    <span class="sr-only">{{ $social['label'] }}</span>
                  </a>
                  @else
                  <span
                    title="{{ $social['label'] }}（请在系统设置中填写链接）"
                    class="block w-[24px] opacity-60"
                  >
                    <img
                      class="w-full h-auto"
                      alt="{{ $social['label'] }}"
                      src="{{ $social['icon'] }}"
                    />
                    <span class="sr-only">{{ $social['label'] }}</span>
                  </span>
                  @endif
                </li>
                @endforeach
              </ul>
              @endif
            </div>
            <div
              class="col-span-4 md:col-span-8 xl:col-span-8 p-4 order-2 md:order-1 xl:order-2 font-inter"
            >
              @if (! empty($footerMenuItems))
              <div
                id="footer-right-section"
                class="footer-nav grid grid-cols-2 md:flex md:flex-wrap mb-8 justify-around gap-12"
              >
                @foreach ($footerMenuItems as $item)
                <nav
                  class="footer-nav-column basis-full flex-auto md:basis-[calc(50%-3rem)] xl:basis-[min-content]"
                  aria-label="{{ $item['title'] }}"
                >
                  <a
                    href="{{ $item['url'] }}"
                    target="{{ $item['target'] }}"
                    tabindex="0"
                    class="footer-nav-item footer-nav-item-title footer-nav-item-title--link hover:underline hover:text-dark-500-hover"
                    title="{{ $item['title'] }}"
                  >
                    <div class="flex items-center gap-1">{{ $item['title'] }}</div>
                  </a>
                  @if (! empty($item['children']))
                  <ul class="footer-nav-ul">
                    @foreach ($item['children'] as $child)
                    <li>
                      <a
                        href="{{ $child['url'] }}"
                        target="{{ $child['target'] }}"
                        tabindex="0"
                        class="footer-nav-item hover:underline hover:text-dark-500-hover"
                        title="{{ $child['title'] }}"
                      >{{ $child['title'] }}</a>
                    </li>
                    @endforeach
                  </ul>
                  @endif
                </nav>
                @endforeach
              </div>
              @endif
            </div>
          </div>
          <div class="footer-copyright-text col-span-12 p-4 flex">
            <div data-rte="true">
              {!! $footerCopyright ?? '' !!}
            </div>
          </div>
        </div>
        <button
          type="button"
          class="fixed !p-0 bottom-3 h-12 w-12 elevation-2 z-[500] transition-all duration-300 left-3 md:left-6 md:bottom-6 hover:scale-110 hover:elevation-6 opacity-0 cta group font-medium uppercase border-2 border-transparent text-link hover:text-link-hover hover:underline focus-visible:underline focus-visible:border-link-focused focus-visible:outline-[4px] focus-visible:outline-link-focused focus-visible:outline focus-visible:ring-transparent disabled:text-grey/50 disabled:cursor-not-allowed disabled:hover:no-underline flex transition-all duration-300 border uppercase text-lg hover:underline focus-visible:underline px-[24px] py-[11.5px] sm:px-[32px] sm:py-[15.5px] rounded-full"
          aria-label="Back to top"
          data-idx="0"
        >
          <div class="flex flex-wrap items-center w-full">
            <div
              class="cta-content flex flex-nowrap items-center justify-center w-full uppercase"
            >
              <div class="rounded-full bg-white text-shadow-off">
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  viewBox="0 0 24 24"
                  fill="currentColor"
                  aria-hidden="true"
                  data-slot="icon"
                  role="none"
                  class="w-full text-oklch(0.3152 0.1176 262.41)"
                >
                  <path
                    fill-rule="evenodd"
                    d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25Zm.53 5.47a.75.75 0 0 0-1.06 0l-3 3a.75.75 0 1 0 1.06 1.06l1.72-1.72v5.69a.75.75 0 0 0 1.5 0v-5.69l1.72 1.72a.75.75 0 1 0 1.06-1.06l-3-3Z"
                    clip-rule="evenodd"
                  ></path></svg
                ><span class="sr-only">Back to top</span>
              </div>
            </div>
          </div>
        </button>
      </footer>
