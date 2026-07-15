@php
    use App\Support\ArticleExtraFields;

    $imageUrl = ArticleExtraFields::listImageUrl($article->extra_fields, $listFields)
        ?? \App\Support\MediaUrl::resolve($article->cover_image ?? null);
    $tags = ArticleExtraFields::listTags($article->extra_fields, $listFields);
    $colClass = match ($loop->index % 3) {
        0 => 'md:col-start-1',
        1 => 'md:col-start-3',
        default => 'md:col-start-5',
    };
    $isHidden = (bool) ($hidden ?? false);
@endphp

<a
    href="{{ route('article.show', $article->slug) }}"
    @class([
        'news-hero-card col-span-2 relative w-full pt-4 pb-8 rounded-2xl overflow-hidden news-card',
        $colClass,
        'news-card--hidden hidden' => $isHidden,
    ])
    data-title="{{ Str::lower($article->title) }}"
>
    <div data-type="hero" class="h-full">
        <div class="relative flex flex-col h-full">
            @if($imageUrl)
                <img
                    src="{{ $imageUrl }}"
                    alt=""
                    loading="lazy"
                    class="mx-auto mb-5 aspect-video rounded-2xl object-cover w-full"
                />
            @endif

            @if(filled($article->published_at))
                <div class="flags flex flex-row md:flex-col md:max-lg:items-start lg:flex-row gap-2 mb-2 items-center justify-start">
                    <span class="text-md inline-block">{{ $article->published_at->format('d/m/Y') }}</span>
                </div>
            @endif

            <div class="title line-clamp-2">
                <h3 class="text-secondary text-xl font-medium">{{ $article->title }}</h3>
            </div>

            @if(filled($article->summary))
                <div class="mt-2 text-lg line-clamp-3 text-left">{{ $article->summary }}</div>
            @endif

            @if($tags !== [])
                <div class="tags flex gap-2 mb-2 pt-4 mt-auto items-center flex-wrap">
                    @foreach ($tags as $tag)
                        <span class="news-tag px-3 h-6 inline-flex items-center text-xs bg-white rounded-full border border-gray-300">
                            {{ $tag }}
                        </span>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</a>
