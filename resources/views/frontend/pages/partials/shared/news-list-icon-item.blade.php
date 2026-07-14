@php
    $item = $item ?? [];
    $isHidden = $isHidden ?? false;
    $title = trim((string) ($item['title'] ?? ''));
    $summary = trim((string) ($item['summary'] ?? ''));
    $icon = trim((string) ($item['icon'] ?? ''));
    $linkTitle = trim((string) ($item['link_title'] ?? ''));
    $url = trim((string) ($item['url'] ?? ''));
    $target = (string) ($item['target'] ?? '');
    $hasLink = filled($url) && filled($linkTitle);
@endphp

<div
    @class([
        'cms-news-list-icon-item relative w-full rounded-lg p-8 flex flex-col items-start overflow-hidden gap-4',
        'cms-news-list-curated__item--hidden' => $isHidden,
    ])
>
    @if(filled($icon))
        <div class="cms-news-list-icon-item__icon relative text-left">
            <img
                src="{{ $icon }}"
                alt=""
                class="cms-news-list-icon-item__icon-image h-16 w-16 object-contain"
                loading="lazy"
                decoding="async"
            />
        </div>
    @endif

    @if(filled($title))
        <h2 class="cms-news-list-icon-item__title mb-0 font-medium font-din text-2xl leading-[1.4] tracking-[.04em] uppercase text-left text-secondary">
            {{ $title }}
        </h2>
    @endif

    @if(filled($summary))
        <p class="cms-news-list-icon-item__summary mb-0 text-lg leading-relaxed text-left text-primary line-clamp-2">
            {{ $summary }}
        </p>
    @endif

    @if($hasLink)
        <a
            href="{{ $url }}"
            @if(filled($target)) target="{{ $target }}" @endif
            @if($target === '_blank') rel="noopener noreferrer" @endif
            class="cms-news-list-icon-item__link mt-4 flex gap-[6px] font-medium text-link hover:text-link-hover transition-all duration-300 group/iconCard text-lg items-center text-left"
        >
            {{ $linkTitle }}
            <span class="shrink-0 group-hover/iconCard:translate-x-2 transition-transform duration-300">
                @include('partials.icons.ipa-link-arrow', ['variant' => 'light'])
            </span>
        </a>
    @endif
</div>
