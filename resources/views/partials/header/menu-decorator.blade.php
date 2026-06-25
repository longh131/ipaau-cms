@php
    $promo = $item['promo'] ?? [];
    $hasImage = filled($promo['image'] ?? null);
    $hasText = filled($promo['text'] ?? null);
@endphp

<div data-type="menu-decorator" @class(['empty:hidden' => ! $hasImage && ! $hasText])>
    @if($hasImage)
        <div>
            <img src="{{ $promo['image'] }}" alt="{{ $promo['image_alt'] ?? '' }}" />
        </div>
    @endif
    @if($hasText)
        <div>
            @if(filled($promo['url'] ?? null))
                <a
                    href="{{ $promo['url'] }}"
                    target="{{ $promo['target'] ?? '_self' }}"
                    class="flex items-center gap-6 px-0 group/navlink"
                >
                    {{ $promo['text'] }}
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" data-slot="icon" role="none" class="h-6 w-6 shrink-0 group-hover/navlink:translate-x-1 transition-all duration-300">
                        <path fill-rule="evenodd" d="M12.97 3.97a.75.75 0 0 1 1.06 0l7.5 7.5a.75.75 0 0 1 0 1.06l-7.5 7.5a.75.75 0 1 1-1.06-1.06l6.22-6.22H3a.75.75 0 0 1 0-1.5h16.19l-6.22-6.22a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd"></path>
                    </svg>
                </a>
            @else
                {{ $promo['text'] }}
            @endif
        </div>
    @endif
</div>
