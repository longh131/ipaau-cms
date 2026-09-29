@extends('layouts.app', [
    'bodyClass' => 'cms-about-page cms-content-page cms-news-list-page cms-events-cpd-list-page',
    'headerBlobPartial' => 'blob-about',
])

@section('title', $category->name)
@section('canonical', route('category.show', $category->slug))
@section('og_title', $category->name)

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/about-ipa-pages.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/news-pages.css') }}" />
@endpush

@section('content')
    <x-breadcrumbs :items="$breadcrumbs ?? []" />

    <section
        data-type="categoryIntro"
        @class([
            'cms-page-content-section cms-category-intro-section',
            'cms-page-content-section--with-breadcrumb' => ! empty($breadcrumbs ?? []),
            'pt-28' => empty($breadcrumbs ?? []),
        ])
        style="
            --bg-color: transparent;
            --ipa-color-light: oklch(0.464 0 0);
            --ipa-color-dark: oklch(1 0 0);
            --light-or-dark: light;
            color: var(--ipa-color-light);
        "
    >
        <div class="inner container px-4 md:px-10 mx-auto">
            <header class="cms-category-intro-section__header text-center max-w-3xl mx-auto">
                <h1 class="font-apex-book cms-section-title text-secondary mb-0">
                    {{ $category->name }}
                </h1>

                @if(filled(strip_tags($introductionHtml ?? '')))
                    <div class="cms-category-intro-section__intro news-rich-text mt-8 text-lg font-din text-left md:text-center">
                        {!! $introductionHtml !!}
                    </div>
                @endif
            </header>
        </div>
    </section>

    <section
        data-type="blogSection"
        class="cms-page-content-section cms-news-list-section cms-events-cpd-listing pb-16"
        style="
            --bg-color: transparent;
            --ipa-color-light: oklch(0.464 0 0);
            --ipa-color-dark: oklch(1 0 0);
            --light-or-dark: light;
            color: var(--ipa-color-light);
        "
    >
        <div class="inner container px-4 md:px-10 mx-auto">
            <div class="cms-events-cpd-listing__layout">
                <aside class="cms-events-cpd-filter" aria-label="按开课日期筛选">
                    <h2 class="cms-events-cpd-filter__heading">按日期筛选</h2>

                    <form
                        method="get"
                        action="{{ route('category.show', $category->slug) }}"
                        class="cms-events-cpd-filter__form"
                    >
                        <label class="cms-events-cpd-filter__label" for="events-cpd-from">开始日期</label>
                        <input
                            id="events-cpd-from"
                            class="cms-events-cpd-filter__input"
                            type="date"
                            name="from"
                            value="{{ $dateFrom ?? '' }}"
                        />

                        <label class="cms-events-cpd-filter__label" for="events-cpd-to">截止日期</label>
                        <input
                            id="events-cpd-to"
                            class="cms-events-cpd-filter__input"
                            type="date"
                            name="to"
                            value="{{ $dateTo ?? '' }}"
                        />

                        <div class="cms-events-cpd-filter__actions">
                            <button type="submit" class="cms-events-cpd-filter__submit">确定</button>
                            <a
                                href="{{ route('category.show', $category->slug) }}"
                                class="cms-events-cpd-filter__clear"
                            >清除</a>
                        </div>
                    </form>
                </aside>

                <div class="cms-events-cpd-results">
                    @if($articles->isEmpty())
                        <p class="cms-events-cpd-results__empty">
                            @if(filled($dateFrom ?? null) || filled($dateTo ?? null))
                                该日期范围内暂无活动。
                            @else
                                暂无内容。
                            @endif
                        </p>
                    @else
                        <div class="cms-events-cpd-results__list">
                            @foreach ($articles as $article)
                                @include('frontend.partials.articles.events-cpd-list-item', [
                                    'article' => $article,
                                    'category' => $category,
                                ])
                            @endforeach
                        </div>

                        @if($articles->hasPages())
                            <nav class="cms-category-pagination mt-12" aria-label="文章分页">
                                {{ $articles->links('frontend.partials.pagination.default') }}
                            </nav>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </section>
@endsection
