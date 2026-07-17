@extends('layouts.app', [
    'bodyClass' => 'cms-content-page cms-leadership-team-page',
    'headerBlobPartial' => 'blob-home',
])

@section('title', $category->name)
@section('canonical', route('category.show', $category->slug))
@section('og_title', $category->name)

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/leadership-team.css') }}" />
@endpush

@section('content')
    <x-breadcrumbs :items="$breadcrumbs ?? []" />

    <section
        data-type="teamIntroHero"
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
                            data-rte="true"
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
                                class="leadership-hero__intro text-[color:var(--ipa-color)] mt-8 text-lg font-din news-rich-text"
                                data-type="section-description"
                            >
                                {!! $introductionHtml !!}
                            </div>
                        @elseif(filled($category->description))
                            <div class="leadership-hero__intro text-[color:var(--ipa-color)] mt-8 text-lg font-din">
                                <p>{{ $category->description }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section
        data-type="teamIntroListing"
        class="leadership-team py-12 bg-[color:var(--bg-color)]"
        style="
            --bg-color: #ffffff;
            --ipa-color-light: oklch(0.464 0 0);
            --ipa-color-dark: oklch(1 0 0);
            --light-or-dark: light;
            color: var(--ipa-color-light);
        "
    >
        <div class="inner container px-4 md:px-10 mx-auto flex flex-col gap-12">
            @if($articles->isEmpty())
                <p class="text-center text-primary text-lg lg:text-xl">暂无内容。</p>
            @else
                <div class="leadership-team__grid grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 lg:gap-10 text-center">
                    @foreach ($articles as $article)
                        @include('frontend.partials.articles.team-member-card', ['article' => $article])
                    @endforeach
                </div>

                @if($articles->hasPages())
                    <nav class="cms-category-pagination mt-4" aria-label="团队分页">
                        {{ $articles->links('frontend.partials.pagination.default') }}
                    </nav>
                @endif
            @endif
        </div>
    </section>
@endsection
