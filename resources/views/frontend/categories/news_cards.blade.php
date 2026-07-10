@extends('layouts.app', ['bodyClass' => 'cms-content-page cms-news-list-page'])

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
        data-type="newsListing"
        @class([
            'cms-page-content-section cms-news-list-section',
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
            <header class="mb-8 lg:mb-10">
                <h1 class="font-apex-book cms-section-title text-secondary">
                    {{ $category->name }}
                </h1>
            </header>

            @if($articles->isEmpty())
                <p class="text-center text-primary text-lg lg:text-xl">暂无内容。</p>
            @else
                <div class="grid grid-cols-1 md:grid-cols-6 items-stretch pt-4 gap-8 news-card-grid">
                    @foreach ($articles as $article)
                        @include('frontend.partials.articles.news-card', [
                            'article' => $article,
                            'listFields' => $listFields ?? [],
                        ])
                    @endforeach
                </div>

                <div class="mt-10">
                    {{ $articles->links() }}
                </div>
            @endif
        </div>
    </section>
@endsection

@push('scripts')
    <script src="{{ asset('assets/js/news-pages.js') }}" defer></script>
@endpush
