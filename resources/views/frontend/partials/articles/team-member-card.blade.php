@php
    use App\Support\ArticleExtraFields;

    $imageUrl = ArticleExtraFields::teamCoverUrl($article->extra_fields, $article->cover_image);
    $jobTitle = ArticleExtraFields::teamJobTitle($article->extra_fields);
@endphp

<article class="leadership-member team-intro-member">
    <div class="leadership-member__photo-wrap mb-6">
        <div class="leadership-member__photo">
            @if($imageUrl)
                <img src="{{ $imageUrl }}" alt="{{ $article->title }}" loading="lazy" width="99" height="99" />
            @endif
        </div>
    </div>

    <div class="label-xl text-secondary mb-4">{{ $article->title }}</div>

    @if(filled($jobTitle))
        <p class="text-xl leadership-member__title">{{ $jobTitle }}</p>
    @endif

    <a href="{{ route('article.show', $article->slug) }}" class="leadership-member__link">
        查看简介
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" class="w-5 h-5 ml-1" role="none">
            <path fill-rule="evenodd" d="M16.28 11.47a.75.75 0 0 1 0 1.06l-7.72 7.72a.75.75 0 1 1-1.06-1.06L14.69 12 7.5 4.81a.75.75 0 0 1 1.06-1.06l7.72 7.72Z" clip-rule="evenodd"></path>
        </svg>
    </a>
</article>
