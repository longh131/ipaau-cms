@extends('layouts.app', [
    'bodyClass' => 'cms-about-page cms-content-page cms-member-spotlight-page',
    'headerBlobPartial' => 'blob-about',
])

@section('title', $category->name)
@section('canonical', route('category.show', $category->slug))
@section('og_title', $category->name)

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/member-spotlight.css') }}" />
@endpush

@section('content')
    <x-breadcrumbs :items="$breadcrumbs ?? []" />

    <section
        data-type="memberSpotlightHero"
        @class([
            'leadership-hero bg-[color:var(--bg-color)]',
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
        <div @class([
            'inner container px-4 md:px-10 mx-auto flex justify-center flex flex-col gap-12',
            'pt-28 pb-16' => empty($breadcrumbs ?? []),
            'pt-12 pb-12 md:pt-16 md:pb-16' => ! empty($breadcrumbs ?? []),
        ])>
            <div class="heroForeground max-w-full flex justify-center items-center gap-8">
                <div class="basis-full max-w-full shrink-0">
                    <div class="text-center container mx-auto">
                        <div
                            data-type="section-title"
                            class="font-apex-book"
                            style="
                                --ipa-color-light: oklch(0.3152 0.1176 262.41);
                                --ipa-color-dark: oklch(0.9011 0.0552 218.07);
                                color: var(--ipa-color-light);
                            "
                        >
                            <h1 class="text-display-xl lg:text-display-2xl text-secondary">
                                {{ $category->name }}
                            </h1>
                        </div>

                        @if(filled(strip_tags($introductionHtml ?? '')))
                            <div
                                class="leadership-hero__intro text-[color:var(--ipa-color)] mt-8 text-lg font-din news-rich-text max-w-3xl mx-auto"
                                data-type="section-description"
                            >
                                {!! $introductionHtml !!}
                            </div>
                        @elseif(filled($category->description))
                            <div class="leadership-hero__intro text-[color:var(--ipa-color)] mt-8 text-lg font-din max-w-3xl mx-auto">
                                <p>{{ $category->description }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section
        data-type="memberSpotlightListing"
        class="member-spotlight-listing pb-16 bg-[color:var(--bg-color)]"
        style="
            --bg-color: #ffffff;
            --ipa-color-light: oklch(0.464 0 0);
            --ipa-color-dark: oklch(1 0 0);
            --light-or-dark: light;
            color: var(--ipa-color-light);
        "
    >
        @if($articles->isEmpty())
            <div class="inner container px-4 md:px-10 mx-auto">
                <p class="text-center text-primary text-lg lg:text-xl">暂无内容。</p>
            </div>
        @else
            @foreach ($articles as $article)
                @include('frontend.partials.articles.member-spotlight-card', ['article' => $article])
            @endforeach

            @if($articles->hasPages())
                <nav class="inner container px-4 md:px-10 mx-auto cms-category-pagination mt-4" aria-label="会员风采分页">
                    {{ $articles->links('frontend.partials.pagination.default') }}
                </nav>
            @endif
        @endif
    </section>
@endsection
