@extends('layouts.app', [
    'bodyClass' => 'cms-about-page cms-content-page cms-news-list-page cms-simple-title-list-page',
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
        class="cms-page-content-section cms-news-list-section cms-events-cpd-listing cms-events-cpd-listing--titles-only pb-16"
        style="
            --bg-color: transparent;
            --ipa-color-light: oklch(0.464 0 0);
            --ipa-color-dark: oklch(1 0 0);
            --light-or-dark: light;
            color: var(--ipa-color-light);
        "
    >
        <div class="inner container px-4 md:px-10 mx-auto">
            @if($articles->isEmpty())
                <p class="cms-events-cpd-results__empty text-center">暂无内容。</p>
            @else
                <div class="cms-events-cpd-results__list">
                    @foreach ($articles as $article)
                        <article class="cms-events-cpd-result">
                            <h2 class="cms-events-cpd-result__title">
                                <a href="{{ route('article.show', $article->slug) }}">{{ $article->title }}</a>
                            </h2>
                        </article>
                    @endforeach
                </div>

                @if($articles->hasPages())
                    <nav class="cms-category-pagination mt-12" aria-label="文章分页">
                        {{ $articles->links('frontend.partials.pagination.default') }}
                    </nav>
                @endif
            @endif
        </div>
    </section>
@endsection
