@props([
    'title',
    'url' => null,
    'imageDesktop' => null,
    'imageMobile' => null,
    'showArrow' => false,
])

@php
    $cardClass = 'relative aspect-square basis-[var(--ipa-card-basis-sm)] md:basis-[var(--ipa-card-basis-md)] lg:basis-[var(--ipa-card-basis-lg)] flex flex-col bg-electric-blue break-word w-full overflow-hidden rounded-3xl cardItem text-left aspect-square';
    $isLink = filled($url);
    $linkClass = $isLink
        ? ' hover:scale-[1.05] transition-all duration-300 hover:elevation-4 elevation-6'
        : '';
@endphp

@if ($isLink)
<a href="{{ $url }}" class="{{ $cardClass }}{{ $linkClass }}">
@else
<div class="{{ $cardClass }}">
@endif
    @if(filled($imageDesktop))
    <picture>
        @if(filled($imageMobile) && $imageMobile !== $imageDesktop)
        <source srcset="{{ $imageMobile }}" media="screen and (max-width: 767px)" alt="" />
        @endif
        <source srcset="{{ $imageDesktop }}" media="screen and (min-width: 768px)" alt="" />
        <img
            loading="lazy"
            class="w-full h-full absolute top-0 left-0 object-cover"
            src="{{ $imageDesktop }}"
            alt=""
        />
    </picture>
    @endif
    <div class="relative mt-auto p-4 md:p-6 max-md:break-words w-full uppercase text-white flex flex-col md:max-lg:gap-2 gap-5">
        <div class="flex justify-between items-center">
            <div class="line-clamp-3 lg:line-clamp-3">{{ $title }}</div>
            @if($isLink && $showArrow)
            <svg
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 24 24"
                fill="currentColor"
                aria-hidden="true"
                data-slot="icon"
                role="none"
                class="h-5 w-5 shrink-0"
            >
                <path
                    fill-rule="evenodd"
                    d="M12.97 3.97a.75.75 0 0 1 1.06 0l7.5 7.5a.75.75 0 0 1 0 1.06l-7.5 7.5a.75.75 0 1 1-1.06-1.06l6.22-6.22H3a.75.75 0 0 1 0-1.5h16.19l-6.22-6.22a.75.75 0 0 1 0-1.06Z"
                    clip-rule="evenodd"
                ></path>
            </svg>
            @endif
        </div>
    </div>
@if ($isLink)
</a>
@else
</div>
@endif
