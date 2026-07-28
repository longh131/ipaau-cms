@extends('layouts.app', [
    'bodyClass' => 'cms-about-page cms-content-page cms-course-list-page',
    'headerBlobPartial' => 'blob-about',
])

@section('title', $category->name)
@section('canonical', route('category.show', $category->slug))
@section('og_title', $category->name)

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/about-ipa-pages.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/news-pages.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/course-table.css') }}" />
@endpush

@section('content')
    <x-breadcrumbs :items="$breadcrumbs ?? []" />

    <section
        data-type="categoryListing"
        @class([
            'bg-[color:var(--bg-color)] cms-page-content-section cms-course-table-section',
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
            <header class="cms-course-table-section__header mb-8 lg:mb-10">
                <h1 class="font-apex-book cms-section-title text-secondary">
                    {{ $category->name }}
                </h1>

                @if(filled(strip_tags($introductionHtml ?? '')))
                    <div class="cms-course-table-section__intro mt-6 max-w-4xl text-primary text-lg">
                        {!! $introductionHtml !!}
                    </div>
                @endif
            </header>

            @if($courses->isEmpty())
                <p class="text-center text-primary text-lg lg:text-xl">暂无课程。</p>
            @else
                @if(session('course_registration_error'))
                    <p class="cms-course-table-notice mb-6 text-center text-primary">
                        {{ session('course_registration_error') }}
                    </p>
                @endif

                @include('frontend.partials.courses.table', ['courses' => $courses])

                @if($courses->hasPages())
                    <nav class="cms-category-pagination mt-12" aria-label="课程分页">
                        {{ $courses->links('frontend.partials.pagination.default') }}
                    </nav>
                @endif
            @endif
        </div>
    </section>
@endsection
