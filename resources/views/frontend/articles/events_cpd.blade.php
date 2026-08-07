@extends('layouts.app', [
    'bodyClass' => 'cms-about-page cms-content-page cms-news-article-page cms-events-cpd-article-page',
    'headerBlobPartial' => 'blob-about',
])

@section('title', $article->title)
@section('canonical', route('article.show', $article->slug))
@section('og_title', $article->title)

@php
    use App\Support\RichContent;

    $bodyHtml = RichContent::toHtml($article->content);
@endphp

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/about-ipa-pages.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/news-pages.css') }}" />
@endpush

@section('content')
    <x-breadcrumbs :items="$breadcrumbs ?? []" />

    <section
        data-type="articleHeader"
        @class([
            'news-section cms-news-article-header cms-events-cpd-article-header bg-[color:var(--bg-color)]',
            'cms-page-content-section--with-breadcrumb' => ! empty($breadcrumbs ?? []),
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
            <div class="container mx-auto px-0 py-10 lg:py-16">
                <div class="cms-news-article-header__content cms-events-cpd-article-header__content text-center mx-auto">
                    @if(filled($article->published_at))
                        <div class="cms-news-article-header__meta font-din text-primary">
                            <time datetime="{{ $article->published_at->toDateString() }}">
                                {{ $article->published_at->format('d/m/Y') }}
                            </time>
                        </div>
                    @endif

                    <div class="news-rich-text cms-news-article-header__title-wrap">
                        <h1 class="cms-news-article-title text-display-lg lg:text-display-xl text-secondary mb-0">
                            {{ $article->title }}
                        </h1>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section
        data-type="articleContainer"
        class="news-section cms-news-article-body cms-events-cpd-article-body bg-[color:var(--bg-color)] py-12 lg:py-16"
        style="
            --bg-color: #F2F2F2;
            --ipa-color-light: oklch(0.464 0 0);
            --ipa-color-dark: oklch(1 0 0);
            --light-or-dark: light;
            color: var(--ipa-color-light);
        "
    >
        <div class="inner container px-4 md:px-10 mx-auto">
            <div class="cms-news-article-body__inner">
                @if(filled($registrationUrl ?? null))
                    <div class="cms-events-cpd-register mb-8">
                        <a
                            href="{{ $registrationUrl }}"
                            class="cms-events-cpd-register__button cta group font-medium uppercase border-2 border-link bg-link text-white hover:bg-link-hover hover:border-link-hover inline-flex transition-all duration-300 uppercase text-lg px-[24px] py-[11.5px] sm:px-[32px] sm:py-[15.5px] rounded-full"
                        >
                            <span class="cta-content flex flex-nowrap items-center justify-center w-full uppercase text-center">
                                我要报名
                            </span>
                        </a>
                    </div>
                @endif

                @if(filled(strip_tags($bodyHtml)))
                    <div class="about-rich-text cms-page-content cms-news-article-body__content font-din text-[color:var(--ipa-color)] news-rich-text" data-rte="true">
                        {!! $bodyHtml !!}
                    </div>
                @endif

                @include('frontend.partials.articles.extra-fields', ['items' => $extraFieldItems ?? []])
            </div>
        </div>
    </section>
@endsection
