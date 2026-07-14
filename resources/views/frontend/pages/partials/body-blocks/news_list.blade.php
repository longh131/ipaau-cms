@php
    use App\Support\PageTemplate\GeneralSecondarySections;

    /** @var array{
     *     section_title: string,
     *     summary_html: string,
     *     view_more_label: string,
     *     section_background: string,
     *     initial_visible: int,
     *     items: array<int, array{
     *         title: string,
     *         summary: string,
     *         icon: ?string,
     *         link_title: string,
     *         url: string,
     *         target: string
     *     }>
     * } $block */
    $block = $block ?? [];
    $layout = $layout ?? 'default';
    $isGeneralSecondary = $layout === 'general_secondary';
    $sectionTitle = trim((string) ($block['section_title'] ?? ''));
    $summaryHtml = (string) ($block['summary_html'] ?? '');
    $hasSummary = filled(strip_tags($summaryHtml));
    $items = $block['items'] ?? [];
    $initialVisible = max(1, (int) ($block['initial_visible'] ?? GeneralSecondarySections::NEWS_LIST_INITIAL_VISIBLE));
    $viewMoreLabel = trim((string) ($block['view_more_label'] ?? '查看更多'));
    $viewMoreLabel = filled($viewMoreLabel) ? $viewMoreLabel : '查看更多';
    $hasHiddenItems = count($items) > $initialVisible;
    $sectionBackground = GeneralSecondarySections::normalizeNewsListBackground(
        (string) ($block['section_background'] ?? GeneralSecondarySections::NEWS_LIST_BG_GRAY),
    );
    $hasGrayBackground = $sectionBackground === GeneralSecondarySections::NEWS_LIST_BG_GRAY;
    $sectionBgColor = $hasGrayBackground
        ? GeneralSecondarySections::NEWS_LIST_BG_GRAY_COLOR
        : 'transparent';
@endphp

@if(filled($sectionTitle) || $hasSummary || $items !== [])
    <section
        data-type="cardListCurated"
        @class([
            'cms-body-block cms-body-block--news-list cms-news-list-curated',
            'cms-governance-module cms-general-secondary-module' => $isGeneralSecondary,
            'cms-news-list-curated--general-secondary' => $isGeneralSecondary,
            'cms-news-list-curated--expandable' => $hasHiddenItems,
        ])
        style="
            --ipa-color-light: oklch(0.464 0 0);
            --ipa-color-dark: oklch(1 0 0);
            --light-or-dark: light;
            color: var(--ipa-color-light);
        "
    >
        <div class="inner container px-4 md:px-10 mx-auto">
            <div
                @class([
                    'cms-news-list-curated__panel rounded-lg',
                    'cms-news-list-curated__panel--gray bg-[color:var(--bg-color)]' => $hasGrayBackground,
                    'cms-news-list-curated--transparent-bg' => ! $hasGrayBackground,
                ])
                style="--bg-color: {{ $sectionBgColor }};"
            >
                <div class="cms-news-list-curated__inner px-4 md:px-10 py-16">
                @if(filled($sectionTitle))
                    <div class="cms-news-list-curated__heading w-full text-left">
                        <h3 class="cms-section-title leading-tight tracking-[-.0253334em] mb-0 text-secondary">
                            {{ $sectionTitle }}
                        </h3>
                    </div>
                @endif

                @if($items !== [])
                    <div @class([
                        'cms-news-list-curated__grid items-start gap-x-10 gap-y-12',
                        'pt-10' => filled($sectionTitle),
                    ])>
                        @foreach ($items as $index => $item)
                            @php
                                $isHidden = $index >= $initialVisible;
                            @endphp

                            @if($isGeneralSecondary)
                                @include('frontend.pages.partials.shared.news-list-icon-item', [
                                    'item' => $item,
                                    'isHidden' => $isHidden,
                                ])
                            @else
                                @php
                                    $url = trim((string) ($item['url'] ?? ''));
                                    $title = trim((string) ($item['title'] ?? ''));
                                    $summary = trim((string) ($item['summary'] ?? ''));
                                    $target = (string) ($item['target'] ?? '');
                                    $isLink = filled($url);
                                @endphp

                                @if($isLink)
                                    <a
                                        href="{{ $url }}"
                                        @if(filled($target)) target="{{ $target }}" @endif
                                        @if($target === '_blank') rel="noopener noreferrer" @endif
                                        @class([
                                            'cms-news-list-curated__item cms-card-list-curated__item relative w-full rounded-lg p-8 flex flex-col self-stretch transition-all duration-300 items-start overflow-hidden border border-[#C6C6C6] group/noImageCard elevation-0 hover:elevation-3 hover:bg-grey-subtle',
                                            'cms-news-list-curated__item--hidden' => $isHidden,
                                        ])
                                        style="--bg-color: #F2F2F2; background-color: var(--bg-color);"
                                    >
                                        @include('frontend.pages.partials.shared.news-list-item-inner', [
                                            'title' => $title,
                                            'summary' => $summary,
                                            'showArrow' => true,
                                        ])
                                    </a>
                                @else
                                    <div
                                        @class([
                                            'cms-news-list-curated__item cms-card-list-curated__item relative w-full rounded-lg p-8 flex flex-col self-stretch transition-all duration-300 items-start overflow-hidden border border-[#C6C6C6]',
                                            'cms-news-list-curated__item--hidden' => $isHidden,
                                        ])
                                        style="--bg-color: #F2F2F2; background-color: var(--bg-color);"
                                    >
                                        @include('frontend.pages.partials.shared.news-list-item-inner', [
                                            'title' => $title,
                                            'summary' => $summary,
                                            'showArrow' => false,
                                        ])
                                    </div>
                                @endif
                            @endif
                        @endforeach
                    </div>
                @endif

                @if($hasSummary)
                    <div
                        class="cms-news-list-curated__intro cms-page-content cms-rich-text__body text-[color:var(--ipa-color-light)] text-xl font-din mt-12"
                        data-rte="true"
                    >
                        {!! $summaryHtml !!}
                    </div>
                @endif

                @if($hasHiddenItems)
                    <div @class([
                        'cms-news-list-curated__actions flex justify-center',
                        'mt-20' => ! $hasSummary,
                        'mt-12' => $hasSummary,
                    ])>
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
            </div>
        </div>
    </section>
@endif
