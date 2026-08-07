@extends('layouts.app', [
    'bodyClass' => 'cms-about-page cms-content-page cms-video-article-page',
    'headerBlobPartial' => 'blob-about',
])

@section('title', $article->title)
@section('canonical', route('article.show', $article->slug))
@section('og_title', $article->title)

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/about-ipa-pages.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/news-pages.css') }}" />
@endpush

@section('content')
    <x-breadcrumbs :items="$breadcrumbs ?? []" />

    <section
        data-type="articleHeader"
        @class([
            'news-section cms-news-article-header cms-video-article-header bg-[color:var(--bg-color)]',
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
                <div class="cms-news-article-header__content cms-video-article-header__content text-center mx-auto">
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
        class="news-section cms-news-article-body cms-video-article-body bg-[color:var(--bg-color)] py-12 lg:py-16"
        style="
            --bg-color: #F2F2F2;
            --ipa-color-light: oklch(0.464 0 0);
            --ipa-color-dark: oklch(1 0 0);
            --light-or-dark: light;
            color: var(--ipa-color-light);
        "
    >
        <div class="inner container px-4 md:px-10 mx-auto">
            <div class="cms-video-article-body__inner">
                @if(filled($videoUrl ?? null))
                    <div class="cms-video-player-wrap">
                        <video
                            controls
                            preload="metadata"
                            playsinline
                            class="cms-video-player w-full rounded-2xl"
                            @if(filled($posterUrl ?? null)) poster="{{ $posterUrl }}" @endif
                        >
                            <source src="{{ $videoUrl }}" type="{{ $videoMimeType ?? 'video/mp4' }}">
                        </video>
                    </div>
                @else
                    <p class="text-center text-primary text-lg">暂无视频。</p>
                @endif
            </div>
        </div>
    </section>
@endsection
