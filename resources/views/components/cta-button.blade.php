@props([
    'label',
    'url',
    'style' => 'secondary',
    'target' => null,
])

@php
    $isPrimary = $style === 'primary';
    $classes = $isPrimary
        ? 'max-sm:w-full cta group font-medium uppercase border-2 border-link bg-link text-white hover:bg-link-hover hover:border-link-hover focus-visible:bg-link-hover focus-visible:border-link-focused focus-visible:outline-[4px] focus-visible:outline-link-focused focus-visible:outline focus-visible:ring-transparent disabled:bg-disabled disabled:border-disabled disabled:text-grey disabled:hover:no-underline disabled:cursor-not-allowed flex transition-all duration-300 border uppercase text-lg hover:underline focus-visible:underline px-[24px] py-[11.5px] sm:px-[32px] sm:py-[15.5px] rounded-full'
        : 'max-sm:w-full cta group font-medium uppercase border-2 border-link bg-white text-link hover:bg-link-hover hover:text-white focus-visible:bg-link-hover focus-visible:text-white focus-visible:border-link-focused focus-visible:outline-[4px] focus-visible:outline-link-focused focus-visible:outline focus-visible:ring-transparent focus-visible:no-underline disabled:bg-disabled disabled:border-grey disabled:text-grey disabled:hover:no-underline disabled:cursor-not-allowed flex transition-all duration-300 border uppercase text-lg hover:underline focus-visible:underline px-[24px] py-[11.5px] sm:px-[32px] sm:py-[15.5px] rounded-full';
@endphp

<a
    href="{{ $url }}"
    @if(filled($target)) target="{{ $target }}" @endif
    @if($target === '_blank') rel="noopener noreferrer" @endif
    class="{{ $classes }}"
    tabindex="0"
>
    <div class="flex flex-wrap items-center w-full">
        <div class="cta-content flex flex-nowrap items-center justify-center w-full uppercase">
            {{ $label }}
        </div>
    </div>
</a>
