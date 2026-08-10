@php
    use App\Support\ArticleExtraFields;
    use App\Support\CategoryListTemplate\MemberSpotlightTemplate;
    use App\Support\MediaUrl;

    $position = ArticleExtraFields::memberPosition($article->extra_fields);
    $coverUrl = MediaUrl::resolve($article->cover_image);
@endphp

<section
    data-type="basicContentWithColumns"
    class="member-spotlight-member py-12 bg-[color:var(--bg-color)]"
    style="
        --bg-color: #FFFFFF;
        --ipa-color-light: oklch(0.464 0 0);
        --ipa-color-dark: oklch(1 0 0);
        --light-or-dark: light;
        color: var(--ipa-color-light);
    "
>
    <div class="inner container px-4 md:px-10 mx-auto flex flex-col gap-12">
        <div class="column-wrapper member-spotlight-member__grid text-left">
            <div class="column flex flex-col h-full pb-5 justify-start">
                @if($coverUrl)
                    <div class="member-spotlight-member__photo text-left">
                        <a href="{{ route('article.show', $article->slug) }}" class="block">
                            <img
                                src="{{ $coverUrl }}"
                                alt="{{ $article->title }}"
                                width="210"
                                loading="lazy"
                            />
                        </a>
                    </div>
                @endif
            </div>

            <div class="column flex flex-col h-full pb-5 justify-start">
                <div class="member-spotlight-member__body text-left" data-type="section-description">
                    <h5 class="text-display-md lg:text-display-lg" style="text-align: left;">
                        <strong>
                            <a href="{{ route('article.show', $article->slug) }}" class="text-secondary hover:text-link">
                                {{ $article->title }}
                            </a>
                        </strong>
                    </h5>

                    @if(filled($position))
                        <h5 class="text-md member-spotlight-member__position text-primary font-din" style="text-align: left;">
                            {{ $position }}
                        </h5>
                    @endif

                    @if(filled($article->summary))
                        <p class="text-md font-din text-primary" style="text-align: left;">
                            {{ $article->summary }}
                        </p>
                    @endif

                    <a href="{{ route('article.show', $article->slug) }}" class="member-spotlight-member__link font-din">
                        查看完整内容
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" class="w-5 h-5 ml-1" role="none">
                            <path fill-rule="evenodd" d="M16.28 11.47a.75.75 0 0 1 0 1.06l-7.72 7.72a.75.75 0 1 1-1.06-1.06L14.69 12 7.5 4.81a.75.75 0 0 1 1.06-1.06l7.72 7.72Z" clip-rule="evenodd"></path>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
