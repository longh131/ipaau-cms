@extends('layouts.app', ['bodyClass' => 'cms-content-page cms-news-list-page cms-search-page'])

@section('title', filled($query) ? '搜索：'.$query : '搜索')
@section('canonical', route('search', filled($query) ? ['q' => $query] : []))
@section('og_title', filled($query) ? '搜索：'.$query : '搜索')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/about-ipa-pages.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/news-pages.css') }}" />
@endpush

@section('content')
    <x-breadcrumbs :items="$breadcrumbs ?? []" />

    <section
        data-type="searchResult"
        @class([
            'cms-page-content-section cms-news-list-section cms-search-section',
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
                    @if(filled($query))
                        搜索结果
                    @else
                        搜索
                    @endif
                </h1>

                @if(filled($query))
                    <p class="mt-4 text-primary text-lg font-din">
                        关键词「{{ $query }}」共找到 {{ $articles->total() }} 条结果
                    </p>
                @else
                    <p class="mt-4 text-primary text-lg font-din">
                        请在顶部搜索框输入关键词。
                    </p>
                @endif
            </header>

            @if(filled($query))
                @if($articles->isEmpty())
                    <p class="text-center text-primary text-lg lg:text-xl">未找到相关内容，请尝试其他关键词。</p>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-6 items-stretch pt-4 gap-8 news-card-grid">
                        @foreach ($articles as $article)
                            @include('frontend.partials.articles.news-card', [
                                'article' => $article,
                                'listFields' => \App\Support\ArticleExtraFields::listFields(
                                    $article->category?->article_extra_field_schema,
                                ),
                            ])
                        @endforeach
                    </div>

                    @if($articles->hasPages())
                        <nav class="cms-category-pagination mt-12" aria-label="搜索结果分页">
                            {{ $articles->links('frontend.partials.pagination.default') }}
                        </nav>
                    @endif
                @endif
            @endif
        </div>
    </section>
@endsection

@push('scripts')
    <script src="{{ asset('assets/js/news-pages.js') }}" defer></script>
@endpush
