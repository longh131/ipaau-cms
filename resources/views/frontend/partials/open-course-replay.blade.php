@php
    $replay = $openCourseReplay ?? null;
    $replayArticles = $replay['articles'] ?? collect();
    $replayMoreUrl = $replay['more_url'] ?? null;
@endphp

@if($replayArticles->isNotEmpty())
    <section class="cms-open-course-replay" aria-labelledby="open-course-replay-title">
        <header class="cms-open-course-replay__header text-center">
            <h2 id="open-course-replay-title" class="font-apex-book cms-section-title text-secondary mb-0">
                公开课回放
            </h2>
        </header>

        <div class="grid grid-cols-1 md:grid-cols-6 items-stretch pt-8 gap-8 news-card-grid">
            @foreach ($replayArticles as $article)
                @include('frontend.partials.articles.video-card', [
                    'article' => $article,
                    'playInModal' => true,
                ])
            @endforeach
        </div>

        @if(filled($replayMoreUrl))
            <div class="cms-open-course-replay__footer flex justify-center mt-12">
                <a
                    href="{{ $replayMoreUrl }}"
                    class="cms-video-hub-block__more cta group font-medium uppercase border-2 border-link bg-white text-link hover:bg-link-hover hover:text-white inline-flex transition-all duration-300 uppercase text-lg px-[24px] py-[11.5px] sm:px-[32px] sm:py-[15.5px] rounded-full shrink-0"
                >
                    <span class="cta-content flex flex-nowrap items-center justify-center w-full uppercase text-center">
                        查看更多
                    </span>
                </a>
            </div>
        @endif
    </section>

    @include('frontend.partials.video-modal')
@endif
