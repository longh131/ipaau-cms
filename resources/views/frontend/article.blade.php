@extends('layouts.app', [
    'bodyClass' => 'cms-about-page cms-content-page cms-basic-content-page cms-article-page',
    'headerBlobPartial' => 'blob-about',
])

@section('title', $article->title)
@section('canonical', route('article.show', $article->slug))
@section('og_title', $article->title)

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/about-ipa-pages.css') }}" />
@endpush

@section('content')
    <x-breadcrumbs :items="$breadcrumbs ?? []" />

    <section
        data-type="articleDetail"
        @class([
            'cms-page-content-section cms-basic-content-section',
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
        <div class="inner container px-4 md:px-10 mx-auto cms-basic-content__inner">
            <header class="cms-basic-content__header">
                <h1 class="cms-basic-content__title font-apex-book cms-section-title text-secondary mb-0">
                    {{ $article->title }}
                </h1>

                @if(filled($article->published_at))
                    <p class="cms-basic-content__summary font-din text-primary leading-relaxed">
                        {{ $article->published_at->format('d F Y') }}
                    </p>
                @elseif(filled($article->summary))
                    <p class="cms-basic-content__summary font-din text-primary leading-relaxed">
                        {{ $article->summary }}
                    </p>
                @endif
            </header>

            @php($bodyHtml = \App\Support\RichContent::toHtml($article->content))

            @if(filled(strip_tags($bodyHtml)))
                <div class="about-rich-text cms-page-content cms-basic-content__body cms-basic-content__body--html font-din text-[color:var(--ipa-color)]" data-rte="true">
                    {!! $bodyHtml !!}
                </div>
            @endif

            @include('frontend.partials.articles.extra-fields', ['items' => $extraFieldItems ?? []])
        </div>
    </section>
@endsection
