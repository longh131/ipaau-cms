@php
    use App\Support\CategoryListTemplate\VideoListTemplate;

    $playInModal = (bool) ($playInModal ?? false);
    $imageUrl = VideoListTemplate::posterPublicUrlForArticle($article);
    $videoUrl = $playInModal ? VideoListTemplate::videoPublicUrlForArticle($article) : null;
    $videoMime = $playInModal ? VideoListTemplate::videoMimeType($videoUrl) : null;
    $colClass = match ($loop->index % 3) {
        0 => 'md:col-start-1',
        1 => 'md:col-start-3',
        default => 'md:col-start-5',
    };
    $cardClass = [
        'news-hero-card col-span-2 relative w-full pt-4 pb-8 rounded-2xl overflow-hidden news-card video-card',
        $colClass,
        'video-card--modal' => $playInModal,
    ];
@endphp

@if($playInModal)
    <button
        type="button"
        @class($cardClass)
        data-title="{{ Str::lower($article->title) }}"
        data-video-modal-open
        data-video-src="{{ $videoUrl ?? '' }}"
        data-video-poster="{{ $imageUrl ?? '' }}"
        data-video-title="{{ $article->title }}"
        data-video-type="{{ $videoMime ?? 'video/mp4' }}"
        @disabled(blank($videoUrl))
        aria-label="播放 {{ $article->title }}"
    >
        @include('frontend.partials.articles.video-card-inner', [
            'article' => $article,
            'imageUrl' => $imageUrl,
        ])
    </button>
@else
    <a
        href="{{ route('article.show', $article->slug) }}"
        @class($cardClass)
        data-title="{{ Str::lower($article->title) }}"
    >
        @include('frontend.partials.articles.video-card-inner', [
            'article' => $article,
            'imageUrl' => $imageUrl,
        ])
    </a>
@endif
