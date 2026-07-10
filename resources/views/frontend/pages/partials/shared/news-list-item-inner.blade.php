@if(filled($title ?? null))
    <h2 class="mb-0 font-medium font-din text-2xl leading-[1.4] tracking-[.04em] uppercase inline-flex gap-3 items-center text-link group-hover/noImageCard:underline group-hover/noImageCard:text-link-hover text-left">
        {{ $title }}
        @if($showArrow ?? false)
            <span class="shrink-0 group-hover/noImageCard:translate-x-4 transition-transform duration-300">
                @include('partials.icons.ipa-link-arrow', ['variant' => 'light'])
            </span>
        @endif
    </h2>
@endif

@if(filled($summary ?? null))
    <p class="cms-news-list-curated__summary mt-3 mb-0 text-lg leading-relaxed text-left text-primary line-clamp-3">
        {{ $summary }}
    </p>
@endif
