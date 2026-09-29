@php
    $imageUrl = \App\Support\CategoryListTemplate\VideoListTemplate::posterPublicUrlForArticle($article);
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
    @include('frontend.partials.articles.video-card-inner', [
        'article' => $article,
        'imageUrl' => $imageUrl,
    ])
</a>
