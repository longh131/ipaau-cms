@extends('layouts.app', [
    'bodyClass' => 'cms-about-page cms-content-page cms-course-list-page cms-special-course-list-page',
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
    @php
        use App\Support\RichContent;

        $bodyHtmlTop = (string) ($bodyHtmlTop ?? '');
        $bodyHtmlBottom = (string) ($bodyHtmlBottom ?? '');
        $hasBodyTop = RichContent::hasVisibleHtml($bodyHtmlTop);
        $hasBodyBottom = RichContent::hasVisibleHtml($bodyHtmlBottom);
    @endphp

    <x-breadcrumbs :items="$breadcrumbs ?? []" />

    <section
        data-type="categoryListing"
        @class([
            'bg-[color:var(--bg-color)] cms-page-content-section cms-course-table-section cms-special-course-list-section',
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
            <header class="cms-course-table-section__header cms-category-intro-section__header cms-special-course-list-section__header text-center max-w-3xl mx-auto">
                <h1 class="font-apex-book cms-section-title text-secondary mb-0">
                    {{ $category->name }}
                </h1>

                @if(filled(strip_tags($introductionHtml ?? '')))
                    <div class="cms-category-intro-section__intro cms-course-table-section__intro cms-page-content news-rich-text mt-8 text-lg font-din text-primary text-left md:text-center">
                        {!! $introductionHtml !!}
                    </div>
                @endif
            </header>

            @if($hasBodyTop)
                <div class="cms-special-course-list-section__html-top about-rich-text cms-page-content cms-basic-content__body cms-basic-content__body--html font-din text-[color:var(--ipa-color)]">
                    {!! $bodyHtmlTop !!}
                </div>
            @endif

            @if($courses->isEmpty())
                <p class="cms-special-course-list-section__empty text-center text-primary text-lg lg:text-xl">暂无课程。</p>
            @else
                <div class="cms-special-course-list-section__courses">
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
                </div>
            @endif

            @if($hasBodyBottom)
                <div class="cms-special-course-list-section__html-bottom about-rich-text cms-page-content cms-basic-content__body cms-basic-content__body--html font-din text-[color:var(--ipa-color)]">
                    {!! $bodyHtmlBottom !!}
                </div>
            @endif
        </div>
    </section>
@endsection
