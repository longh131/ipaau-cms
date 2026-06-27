@props([
    'number',
    'title' => null,
    'content' => null,
    'wideOnMobile' => false,
])

<div
    @class([
        'relative bg-white rounded-3xl py-6 lg:px-10 lg:py-24 text-center border-2 border-transparent shadow-sm hover:shadow-md transition-shadow duration-200 flex flex-col justify-center px-4',
        'col-span-2 lg:col-span-1 px-5' => $wideOnMobile,
    ])
    style="
        background:
            linear-gradient(white, white) padding-box padding-box,
            linear-gradient(
                to right,
                rgb(201, 60, 159),
                rgb(240, 95, 34)
            )
            border-box border-box;
    "
>
    <div
        class="text-6xl xl:text-[130px] font-bold font-apex-book text-secondary leading-[1.2] tracking-[-.02em] break-words xl:-mb-2"
    >
        {{ $number }}
    </div>
    @if(filled($title) || filled($content))
    <div
        class="text-base lg:text-[20px] text-warm-plum leading-[1.3] tracking-[.04em] font-medium uppercase break-words"
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
