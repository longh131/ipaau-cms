@php
    /** @var array<int, array{title: string, url: string, target: string}> $cardItems */
    $sectionTitle = trim((string) ($sectionTitle ?? ''));
    $cardItems = $cardItems ?? [];
    $initialVisible = ($initialVisible ?? null) === null ? null : max(1, (int) $initialVisible);
    $viewMoreLabel = trim((string) ($viewMoreLabel ?? '查看更多'));
    $viewMoreLabel = filled($viewMoreLabel) ? $viewMoreLabel : '查看更多';
    $hasHiddenItems = $initialVisible !== null && count($cardItems) > $initialVisible;
@endphp

@if(filled($sectionTitle) || $cardItems !== [])
    <div class="cms-card-list-curated">
        @if(filled($sectionTitle))
            <div class="cms-card-list-curated__heading w-full text-left">
                <h3 class="cms-section-title leading-tight tracking-[-.0253334em] mb-0 text-secondary">
                    {{ $sectionTitle }}
                </h3>
            </div>
        @endif

        @if($cardItems !== [])
            <div @class([
                'cms-card-list-curated__grid grid grid-cols-1 items-start gap-x-10 gap-y-12',
                'pt-10' => filled($sectionTitle),
            ])>
                @foreach ($cardItems as $index => $cardItem)
                    @php
                        $url = trim((string) ($cardItem['url'] ?? ''));
                        $title = trim((string) ($cardItem['title'] ?? ''));
                        $target = (string) ($cardItem['target'] ?? '');
                        $isLink = filled($url);
                        $isHidden = $hasHiddenItems && $index >= $initialVisible;
                    @endphp

                    @if($isLink)
                        <a
                            href="{{ $url }}"
                            @if(filled($target)) target="{{ $target }}" @endif
                            @if($target === '_blank') rel="noopener noreferrer" @endif
                            @class([
                                'cms-card-list-curated__item relative w-full rounded-lg p-8 flex flex-col self-stretch transition-all duration-300 items-start overflow-hidden border border-[#C6C6C6] group/noImageCard elevation-0 hover:elevation-3 hover:bg-grey-subtle',
                                'cms-news-list-curated__item--hidden' => $isHidden,
                            ])
                            style="--bg-color: #F2F2F2; background-color: var(--bg-color);"
                        >
                            @include('frontend.pages.partials.shared.card-list-item-inner', [
                                'title' => $title,
                                'showArrow' => true,
                            ])
                        </a>
                    @else
                        <div
                            @class([
                                'cms-card-list-curated__item relative w-full rounded-lg p-8 flex flex-col self-stretch transition-all duration-300 items-start overflow-hidden border border-[#C6C6C6]',
                                'cms-news-list-curated__item--hidden' => $isHidden,
                            ])
                            style="--bg-color: #F2F2F2; background-color: var(--bg-color);"
                        >
                            @include('frontend.pages.partials.shared.card-list-item-inner', [
                                'title' => $title,
                                'showArrow' => false,
                            ])
                        </div>
                    @endif
                @endforeach
            </div>
        @endif

        @if($hasHiddenItems)
            <div class="cms-news-list-curated__actions flex justify-center mt-20">
                <button
                    type="button"
                    class="cms-news-list-curated__toggle cta group font-medium uppercase border-2 border-link bg-white text-link hover:bg-link-hover hover:text-white flex transition-all duration-300 uppercase text-lg px-[24px] py-[11.5px] sm:px-[32px] sm:py-[15.5px] rounded-full"
                    aria-expanded="false"
                >
                    <span class="cms-news-list-curated__toggle-label flex flex-nowrap items-center justify-center w-full uppercase text-center">
                        {{ $viewMoreLabel }}
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" class="h-6 w-6 ml-2 shrink-0">
                            <path fill-rule="evenodd" d="M16.28 11.47a.75.75 0 0 1 0 1.06l-7.5 7.5a.75.75 0 0 1-1.06-1.06L14.69 12 7.72 5.03a.75.75 0 0 1 1.06-1.06l7.5 7.5Z" clip-rule="evenodd" />
                        </svg>
                    </span>
                </button>
            </div>
        @endif
    </div>
@endif
