<header class="relative container mx-auto">
    @include('partials.header.blob-home')

    <div class="mx-auto h-full flex lg:grid lg:grid-cols-[minmax(0,min-content)_minmax(0,auto)_minmax(0,max-content)_minmax(0,max-content)] justify-between lg:justify-normal lg:gap-x-2 no-wrap items-stretch px-4 xl:px-10">
        <div
            data-type="siteLogo"
            class="basis-1/4 md:basis-1/5 flex max-h-full items-center py-3 pr-3 h-[65px] md:h-[85px]"
        >
            <a href="{{ route('home') }}" title="Go to Homepage" class="h-full flex items-center justify-start">
                <picture class="max-h-full h-full w-auto">
                    <source media="(max-width: 768px)" srcset="{{ asset('assets/img/ipa-logo.png') }}" />
                    <source media="(min-width: 769px)" srcset="{{ asset('assets/img/ipa-logo.png') }}" />
                    <img
                        class="w-auto max-w-none h-full"
                        src="{{ asset('assets/img/ipa-logo.png') }}"
                        alt="Site Logo"
                    />
                </picture>
            </a>
        </div>

        <x-menu :menuItems="$menuItems" variant="desktop" />

        @include('partials.header.search-bar')

        <div class="flex items-center gap-4 mr-4 xl:ml-4 xl:mr-0">
            <a
                href="{{ url('/api/authentication/login?returnUrl=%2F') }}"
                class="cta group font-medium uppercase border-2 border-link bg-transparent text-link hover:bg-link-hover hover:text-white focus-visible:bg-link-hover focus-visible:text-white focus-visible:border-link-focused focus-visible:outline-[4px] focus-visible:outline-link-focused focus-visible:outline focus-visible:ring-transparent focus-visible:no-underline disabled:bg-disabled disabled:border-grey disabled:text-grey disabled:hover:no-underline disabled:cursor-not-allowed flex transition-all duration-300 border uppercase text-lg hover:underline focus-visible:underline p-[9px] rounded-full"
                tabindex="0"
            >
                <div class="flex flex-wrap items-center w-full">
                    <div class="cta-content flex flex-nowrap items-center justify-center w-full uppercase">
                        Sign In
                    </div>
                </div>
            </a>
        </div>

        <div data-type="mobileNavTrigger" class="peer/menutrigger self-center inactive xl:hidden ml-3">
            <button
                type="button"
                class="!px-0 !shadow-none h-full items-center cta group font-medium uppercase border-2 border-transparent text-link hover:text-link-hover hover:underline focus-visible:underline focus-visible:border-link-focused focus-visible:outline-[4px] focus-visible:outline-link-focused focus-visible:outline focus-visible:ring-transparent disabled:text-grey/50 disabled:cursor-not-allowed disabled:hover:no-underline flex transition-all duration-300 border uppercase text-lg hover:underline focus-visible:underline p-[9px] rounded-full"
                data-idx="0"
            >
                <div class="flex flex-wrap items-center w-full">
                    <div class="cta-content flex flex-nowrap items-center justify-center w-full uppercase">
                        <div class="relative group/menu h-6 w-6 inactive">
                            <div class="absolute top-0 left-0">
                                <svg width="24" height="25" viewBox="0 0 24 25" fill="none" xmlns="http://www.w3.org/2000/svg" class="text-secondary group-[.active]/menu:rotate-90 group-[.active]/menu:opacity-0 group-[.inactive]/menu:rotate-0 group-[.inactive]/menu:opacity-100 transition-all duration-1000">
                                    <path d="M21.9998 4.98151L1.9998 4.9815" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"></path>
                                    <path d="M21.9998 12.9815L3.9998 12.9815" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"></path>
                                    <path d="M21.9998 20.9815L6.9998 20.9815" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"></path>
                                </svg>
                            </div>
                            <div class="absolute top-0 left-0">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" data-slot="icon" class="h-6 w-6 text-base-text group-[.active]/menu:rotate-0 group-[.active]/menu:opacity-100 group-[.inactive]/menu:rotate-90 group-[.inactive]/menu:opacity-0 transition-all duration-1000" role="none">
                                    <path fill-rule="evenodd" d="M5.47 5.47a.75.75 0 0 1 1.06 0L12 10.94l5.47-5.47a.75.75 0 1 1 1.06 1.06L13.06 12l5.47 5.47a.75.75 0 1 1-1.06 1.06L12 13.06l-5.47 5.47a.75.75 0 0 1-1.06-1.06L10.94 12 5.47 6.53a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <span class="sr-only">Toggle mobile menu</span>
                        </div>
                    </div>
                </div>
            </button>
        </div>

        <div
            data-type="mobile-navigation-wrapper"
            class="xl:hidden group/wrapper bg-white shadow-md transition-all duration-500 max-h-0 overflow-hidden inactive flex flex-col w-screen left-1/2 -translate-x-1/2"
        >
            <div class="basis-auto grow-0 shrink-1 overflow-y-auto" data-type="mobile-navigation-content">
                <x-menu :menuItems="$menuItems" variant="mobile" />
            </div>
            <div id="mobile-navigation-footer" class="w-full mt-auto basis-max grow-0 shrink-0">
                <div data-type="menu-decorator" class="empty:hidden"></div>
                <div data-type="menu-decorator" class="empty:hidden"></div>
                <div data-type="menu-decorator" class="empty:hidden"></div>
            </div>
        </div>
    </div>
</header>
