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
            @if($isPrimary)
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" data-slot="icon" class="h-6 ml-4 shrink-0 w-6 text-current" role="presentation">
                    <path d="M3.478 2.404a.75.75 0 0 0-.926.941l2.432 7.905H13.5a.75.75 0 0 1 0 1.5H4.984l-2.432 7.905a.75.75 0 0 0 .926.94 60.519 60.519 0 0 0 18.445-8.986.75.75 0 0 0 0-1.218A60.517 60.517 0 0 0 3.478 2.404Z"></path>
                </svg>
            @else
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" data-slot="icon" class="h-6 ml-4 shrink-0 w-6 text-current" role="presentation">
                    <path fill-rule="evenodd" d="M15.75 2.25H21a.75.75 0 0 1 .75.75v5.25a.75.75 0 0 1-1.5 0V4.81L8.03 17.03a.75.75 0 0 1-1.06-1.06L19.19 3.75h-3.44a.75.75 0 0 1 0-1.5Zm-10.5 4.5a1.5 1.5 0 0 0-1.5 1.5v10.5a1.5 1.5 0 0 0 1.5 1.5h10.5a1.5 1.5 0 0 0 1.5-1.5V10.5a.75.75 0 0 1 1.5 0v8.25a3 3 0 0 1-3 3H5.25a3 3 0 0 1-3-3V8.25a3 3 0 0 1 3-3h8.25a.75.75 0 0 1 0 1.5H5.25Z" clip-rule="evenodd"></path>
                </svg>
            @endif
        </div>
    </div>
</a>
