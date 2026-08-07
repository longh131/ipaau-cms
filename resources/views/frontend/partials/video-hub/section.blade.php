@php
    /** @var array{title: string, slug: string, more_url: string, articles: \Illuminate\Support\Collection} $section */
    $section = $section ?? [];
    $sectionTitle = trim((string) ($section['title'] ?? ''));
    $moreUrl = trim((string) ($section['more_url'] ?? ''));
    $articles = $section['articles'] ?? collect();
@endphp

@if($sectionTitle !== '' || $articles->isNotEmpty())
    <section class="cms-video-hub-block" data-video-hub-section="{{ $section['slug'] ?? '' }}">
        @if($sectionTitle !== '')
            <div class="cms-video-hub-block__header">
                <h2 class="cms-video-hub-block__title font-apex-book cms-section-title text-secondary mb-0">
                    {{ $sectionTitle }}
                </h2>
            </div>
        @endif

        @if($articles->isEmpty())
            <p class="cms-video-hub-block__empty text-primary text-lg mt-8">暂无内容。</p>
        @else
            <div class="grid grid-cols-1 md:grid-cols-6 items-stretch pt-8 gap-8 news-card-grid">
                @foreach ($articles as $article)
                    @include('frontend.partials.articles.video-card', ['article' => $article])
                @endforeach
            </div>
        @endif

        @if($moreUrl !== '')
            <div class="cms-video-hub-block__footer flex justify-center mt-12 lg:mt-16">
                <a
                    href="{{ $moreUrl }}"
                    class="cms-video-hub-block__more cta group font-medium uppercase border-2 border-link bg-white text-link hover:bg-link-hover hover:text-white inline-flex transition-all duration-300 uppercase text-lg px-[24px] py-[11.5px] sm:px-[32px] sm:py-[15.5px] rounded-full shrink-0"
                >
                    <span class="cta-content flex flex-nowrap items-center justify-center w-full uppercase text-center">
                        查看更多
                    </span>
                </a>
            </div>
        @endif
    </section>
@endif
