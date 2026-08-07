@php
    $imageUrl = \App\Support\MediaUrl::resolve($article->cover_image ?? null);
    $colClass = match ($loop->index % 3) {
        0 => 'md:col-start-1',
        1 => 'md:col-start-3',
        default => 'md:col-start-5',
    };
@endphp

<a
    href="{{ route('article.show', $article->slug) }}"
    @class([
        'news-hero-card col-span-2 relative w-full pt-4 pb-8 rounded-2xl overflow-hidden news-card video-card',
        $colClass,
    ])
    data-title="{{ Str::lower($article->title) }}"
>
    <div data-type="hero" class="h-full">
        <div class="relative flex flex-col h-full">
            @if($imageUrl)
                <div class="relative mx-auto mb-5 aspect-video rounded-2xl overflow-hidden w-full">
                    <img
                        src="{{ $imageUrl }}"
                        alt=""
                        loading="lazy"
                        class="h-full w-full object-cover"
                    />
                    <span class="video-card__play" aria-hidden="true"></span>
                </div>
            @else
                <div class="video-card__placeholder mx-auto mb-5 aspect-video rounded-2xl w-full flex items-center justify-center" aria-hidden="true">
                    <span class="video-card__play video-card__play--large"></span>
                </div>
            @endif

            @if(filled($article->published_at))
                <div class="flags flex flex-row md:flex-col md:max-lg:items-start lg:flex-row gap-2 mb-2 items-center justify-start">
                    <span class="text-md inline-block">{{ $article->published_at->format('d/m/Y') }}</span>
                </div>
            @endif

            <div class="title line-clamp-2">
                <h3 class="text-secondary text-xl font-medium">{{ $article->title }}</h3>
            </div>
        </div>
    </div>
</a>
