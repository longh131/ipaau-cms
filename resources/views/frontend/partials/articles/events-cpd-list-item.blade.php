@php
    use App\Support\CategoryListTemplate\EventsCpdTemplate;

    $startDate = EventsCpdTemplate::startDate($article->extra_fields, $category->article_extra_field_schema ?? null);
@endphp

<article class="cms-events-cpd-result">
    @if($startDate)
        <p class="cms-events-cpd-result__meta">
            <time datetime="{{ $startDate->toDateString() }}">{{ $startDate->format('d/m/Y') }}</time>
        </p>
    @endif

    <h2 class="cms-events-cpd-result__title">
        <a href="{{ route('article.show', $article->slug) }}">{{ $article->title }}</a>
    </h2>
</article>
