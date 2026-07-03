@props([
    'numberType' => 'text',
    'number' => null,
    'numberImage' => null,
    'title' => null,
    'content' => null,
    'wideOnMobile' => false,
])

@php
    $isImage = $numberType === 'image' && filled($numberImage);
    $displayNumber = trim((string) ($number ?? ''));
    $hasPlusSuffix = ! $isImage && str_ends_with($displayNumber, '+');
    $numberMain = $hasPlusSuffix ? substr($displayNumber, 0, -1) : $displayNumber;
@endphp

<div
    @class([
        'stats-card relative rounded-3xl py-4 lg:py-6 px-4 lg:px-6 text-center shadow-sm hover:shadow-md transition-shadow duration-200 flex flex-col justify-center',
        'col-span-2 lg:col-span-1 px-5' => $wideOnMobile,
    ])
>
    @if($isImage)
        <div class="stats-card__media flex items-center justify-center mb-2">
            <img
                src="{{ $numberImage }}"
                alt=""
                class="stats-card__image max-h-16 sm:max-h-20 lg:max-h-24 w-auto object-contain"
            />
        </div>
    @elseif(filled($displayNumber))
        <div
            @class([
                'stats-card__number font-bold font-apex-book text-secondary tracking-[-.02em] break-words',
                'stats-card__number--spaced' => filled($title) || filled($content),
            ])
        >
            {{ $numberMain }}@if($hasPlusSuffix)<sup class="top-[-0.42em]">+</sup>@endif
        </div>
    @endif

    @if(filled($title) || filled($content))
        <div
            class="text-sm lg:text-base text-warm-plum leading-[1.3] tracking-[.04em] font-medium uppercase break-words"
        >
            @if(filled($title))
                <div>{{ $title }}</div>
            @endif
            @if(filled($content))
                <div @class(['mt-2 normal-case font-normal tracking-normal text-base lg:text-lg' => filled($title)])>
                    {{ $content }}
                </div>
            @endif
        </div>
    @endif
</div>
