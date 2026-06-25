@props(['menuItems', 'variant' => 'desktop'])

<nav data-type="{{ $variant === 'mobile' ? 'mobile-navigation' : 'desktop-navigation' }}">
    <ul data-type="menu-level-0">
        @foreach($menuItems as $index => $item)
            @php
                $promo = $item['promo'] ?? [];
                $hasChildren = ! empty($item['children']);
            @endphp
            <li data-children="{{ $hasChildren ? 'true' : 'false' }}" data-level="0" class="inactive group/navlink">
                <div data-type="menu-wrapper-0">
                    <a
                        href="{{ $item['url'] }}"
                        target="{{ $item['target'] }}"
                        data-img-description="{{ $promo['text'] ?? '' }}"
                        data-img-alt="{{ $promo['image_alt'] ?? '' }}"
                        data-img="{{ $promo['image'] ?? '' }}"
                        class="h-full flex font-inter font-semibold items-center hover:underline focus:-outline-offset-4 underline-offset-8 text-secondary max-xl:border-b max-xl:border-b-grey-subtle max-xl:py-8"
                        data-idx="{{ $index }}"
                    >
                        {{ $item['title'] }}
                    </a>
                    @if($hasChildren)
                        <button type="button" class="w-12 h-12 ml-auto xl:hidden relative flex items-center justify-center mr-4" aria-label="展开 {{ $item['title'] }} 子菜单">
                            <div class="w-6 h-6 flex items-center justify-center rounded-full border-2 border-secondary text-warm-plum" role="none">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true" data-slot="icon" class="w-4 h-4" role="none">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"></path>
                                </svg>
                            </div>
                        </button>
                    @endif
                </div>
                @if($hasChildren)
                    <div data-type="megamenu-panel" id="megamenu-{{ md5((string) $item['id']) }}" class="megamenu-gradient xl:elevation-bottom-4 xl:w-screen xl:left-1/2 xl:-translate-x-1/2 hidden">
                        <div data-type="megamenu-container" class="container w-full flex items-stretch mx-auto">
                            <ul data-type="megamenu-level-1" class="grow max-xl:py-6">
                                @foreach($item['children'] as $childIndex => $child)
                                    @php $childHasChildren = ! empty($child['children']); @endphp
                                    <li
                                        data-idx="{{ $childIndex }}"
                                        data-sub="-1"
                                        class="inactive xl:text-oklch(1 0 0)-text hover:bg-oklch(1 0 0) hover:text-oklch(1 0 0)-text"
                                    >
                                        <div data-type="menu-wrapper-1" class="pr-4 max-xl:flex max-xl:justify-between max-xl:items-center">
                                            <a
                                                href="{{ $child['url'] }}"
                                                data-idx="{{ $childIndex }}"
                                                data-children="{{ $childHasChildren ? 'true' : 'false' }}"
                                                target="{{ $child['target'] }}"
                                                tabindex="-1"
                                                aria-hidden="true"
                                                class="py-0 xl:py-3 max-xl:h-12 max-xl:flex max-xl:items-center w-full hover:underline text-primary max-xl:font-semibold xl:text-secondary"
                                            >
                                                {{ $child['title'] }}
                                            </a>
                                            @if($childHasChildren)
                                                <button type="button" class="flex items-center justify-center min-w-12 h-12 ml-auto xl:hidden relative" aria-label="展开 {{ $child['title'] }} 子菜单">
                                                    <div class="w-6 h-6 flex items-center justify-center rounded-full border-2 border-secondary text-warm-plum" role="none">
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true" data-slot="icon" class="w-4 h-4">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"></path>
                                                        </svg>
                                                    </div>
                                                </button>
                                            @endif
                                        </div>
                                        @if($childHasChildren)
                                            <ul data-type="megamenu-level-2" id="megamenu-lv2-{{ md5((string) $child['id']) }}" class="inactive max-xl:hidden">
                                                @foreach($child['children'] as $grandchild)
                                                    <li data-level="2">
                                                        <a
                                                            href="{{ $grandchild['url'] }}"
                                                            target="{{ $grandchild['target'] }}"
                                                            class="font-normal m-0 p-2 xl:py-3 flex justify-between xl:border-b-4 text-primary! hover:underline border-transparent transition-all duration-300 focus:-outline-offset-4"
                                                            tabindex="-1"
                                                            aria-hidden="true"
                                                        >
                                                            {{ $grandchild['title'] }}
                                                        </a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                            @include('partials.header.menu-decorator', ['item' => $item])
                        </div>
                    </div>
                @endif
            </li>
        @endforeach
    </ul>
</nav>
