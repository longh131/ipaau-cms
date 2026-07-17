@extends('layouts.app', [
    'bodyClass' => 'cms-about-page cms-content-page cms-leadership-bio-page',
    'headerBlobPartial' => 'blob-about',
])

@section('title', $article->title)
@section('canonical', route('article.show', $article->slug))
@section('og_title', $article->title)

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
            <div class="text-center container mx-auto">
                <div class="font-apex-book">
                    <h1 class="text-display-xl lg:text-display-2xl text-secondary">
                        {{ $category?->name }}
                    </h1>
                </div>

                @if(filled(strip_tags($introductionHtml ?? '')))
                    <div class="leadership-hero__intro text-[color:var(--ipa-color)] mt-8 text-lg font-din news-rich-text">
                        {!! $introductionHtml !!}
                    </div>
                @elseif(filled($category?->description))
                    <div class="leadership-hero__intro text-[color:var(--ipa-color)] mt-8 text-lg font-din">
                        <p>{{ $category->description }}</p>
                    </div>
                @endif
            </div>
        </div>
    </section>

    <section
        data-type="teamIntroBio"
        class="leadership-bio py-12 overflow-hidden bg-[color:var(--bg-color)]"
        style="
            --bg-color: #ffffff;
            --ipa-color-light: oklch(0.464 0 0);
            --ipa-color-dark: oklch(1 0 0);
            --light-or-dark: light;
            color: var(--ipa-color-light);
        "
    >
        <div class="inner container px-4 md:px-10 mx-auto">
            <div class="container mx-auto px-4 py-8 lg:py-12">
                <div class="grid grid-cols-1 lg:grid-cols-[40%_1fr] items-center gap-14 lg:gap-20">
                    <div class="content-section content-section-1 content-section-1--image row-start-1 col-start-1">
                        @if(filled($coverUrl ?? null))
                            <div class="bio-profile__image">
                                <img
                                    src="{{ $coverUrl }}"
                                    alt="{{ $article->title }}"
                                    loading="lazy"
                                />
                            </div>
                        @endif
                    </div>

                    <div class="content-section content-section-2 lg:row-start-1 lg:col-start-2">
                        <div class="text-left container mx-auto team-bio__content">
                            @php
                                $displayJobTitle = $jobTitle ?? \App\Support\ArticleExtraFields::teamJobTitle($article->extra_fields);
                            @endphp

                            @if(filled($displayJobTitle))
                                <span class="eyebrow-xl team-bio__job-title">{{ $displayJobTitle }}</span>
                            @endif

                            <div
                                data-type="section-title"
                                data-rte="true"
                                class="font-apex-book team-bio__name-wrap"
                            >
                                <h2 class="text-display-xl lg:text-display-2xl text-secondary" id="team-member-title">
                                    {{ $article->title }}
                                </h2>
                            </div>

                            @php($bodyHtml = \App\Support\RichContent::toHtml($article->content))

                            @if(filled(strip_tags($bodyHtml)))
                                <div
                                    class="bio-profile__body text-[color:var(--ipa-color)] mt-8 text-xl font-din about-rich-text"
                                    data-type="section-description"
                                    data-rte="true"
                                >
                                    {!! $bodyHtml !!}
                                </div>
                            @elseif(filled($article->summary))
                                <div class="bio-profile__body text-[color:var(--ipa-color)] mt-8 text-xl font-din">
                                    <p>{{ $article->summary }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
