@extends('layouts.app', ['bodyClass' => 'cms-content-page'])

@section('title', $category->name)
@section('canonical', route('category.show', $category->slug))
@section('og_title', $category->name)

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/about-ipa-pages.css') }}" />
@endpush

@section('content')
    <x-breadcrumbs :items="$breadcrumbs ?? []" />

    <section
        data-type="categoryListing"
        @class([
            'bg-[color:var(--bg-color)] cms-page-content-section',
            'cms-page-content-section--with-breadcrumb' => ! empty($breadcrumbs ?? []),
            'py-16 lg:py-24' => empty($breadcrumbs ?? []),
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
            <header class="max-w-4xl mx-auto mb-10 lg:mb-14">
                <h1 class="font-apex-book cms-section-title text-secondary">
                    {{ $category->name }}
                </h1>
            </header>

            @if($articles->isEmpty())
                <p class="text-center text-primary text-lg lg:text-xl">暂无内容。</p>
            @else
                <ul class="max-w-4xl mx-auto space-y-6">
                    @foreach ($articles as $article)
                        <li class="border-b border-grey-subtle pb-6">
                            <a
                                href="{{ route('article.show', $article->slug) }}"
                                class="font-apex-book text-2xl text-secondary hover:underline"
                            >
                                {{ $article->title }}
                            </a>
                            @if(filled($article->published_at))
                                <p class="mt-1 text-sm text-primary">{{ $article->published_at->format('d/m/Y') }}</p>
                            @endif
                            @if(filled($article->summary))
                                <p class="mt-2 text-primary text-lg lg:text-xl">{{ $article->summary }}</p>
                            @endif
                        </li>
                    @endforeach
                </ul>

                <div class="max-w-4xl mx-auto mt-10">
                    {{ $articles->links() }}
                </div>
            @endif
        </div>
    </section>
@endsection
