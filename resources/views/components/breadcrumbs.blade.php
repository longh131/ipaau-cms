@props([
    'items' => [],
])

@php
    $items = is_array($items) ? $items : [];
    $homeItem = ($items[0]['is_home'] ?? false) ? array_shift($items) : null;
    $trailItems = array_values($items);
@endphp

@if($homeItem || $trailItems !== [])
    <nav
        aria-label="breadcrumb navigation"
        class="cms-breadcrumb container mx-auto relative z-10 flex items-center px-7 md:px-10 pt-4 md:pt-5 pb-3"
    >
        <ul class="bg-transparent text-primary font-din w-full flex-nowrap text-md flex gap-3 items-center">
            @if($homeItem)
                <li class="inline-flex items-center gap-1 md:gap-3">
                    <a href="{{ $homeItem['url'] ?? route('home') }}" class="hover:underline px-0 mx-0 flex items-center gap-1 md:gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" class="w-4 h-4">
                            <path d="M11.47 3.841a.75.75 0 0 1 1.06 0l8.69 8.69a.75.75 0 1 0 1.06-1.061l-8.689-8.69a2.25 2.25 0 0 0-3.182 0l-8.69 8.69a.75.75 0 1 0 1.061 1.06l8.69-8.689Z"></path>
                            <path d="m12 5.432 8.159 8.159c.03.03.06.058.091.086v6.198c0 1.035-.84 1.875-1.875 1.875H15a.75.75 0 0 1-.75-.75v-4.5a.75.75 0 0 0-.75-.75h-3a.75.75 0 0 0-.75.75V21a.75.75 0 0 1-.75.75H5.625a1.875 1.875 0 0 1-1.875-1.875v-6.198a2.29 2.29 0 0 0 .091-.086L12 5.432Z"></path>
                        </svg>
                        <span class="sr-only">{{ $homeItem['label'] ?? 'Home' }}</span>
                    </a>
                    @if($trailItems !== [])
                        <span>/</span>
                    @endif
                </li>
            @endif

            @foreach ($trailItems as $index => $item)
                <li class="inline-flex items-center gap-1 md:gap-3">
                    @if($item['is_current'] ?? false)
                        <span class="text-link font-medium inline-block max-md:max-w-16 md:max-lg:max-w-28 truncate text-nowrap ellipsis">
                            {{ $item['label'] }}
                        </span>
                    @else
                        <a
                            href="{{ $item['url'] }}"
                            class="hover:underline px-0 mx-0 inline-block max-md:max-w-16 md:max-lg:max-w-28 truncate text-nowrap ellipsis"
                        >{{ $item['label'] }}</a>
                    @endif

                    @if(! ($item['is_current'] ?? false))
                        <span>/</span>
                    @endif
                </li>
            @endforeach
        </ul>
    </nav>
@endif
