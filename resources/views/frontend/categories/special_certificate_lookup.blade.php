@extends('layouts.app', [
    'bodyClass' => 'cms-about-page cms-content-page cms-special-certificate-lookup-page',
    'headerBlobPartial' => 'blob-about',
])

@section('title', $category->name)
@section('canonical', route('category.show', $category->slug))
@section('og_title', $category->name)

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/about-ipa-pages.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/news-pages.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/course-table.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/certificate-lookup.css') }}" />
@endpush

@section('content')
    @php
        use App\Support\RichContent;

        $bodyHtmlTop = (string) ($bodyHtmlTop ?? '');
        $bodyHtmlBottom = (string) ($bodyHtmlBottom ?? '');
        $hasBodyTop = RichContent::hasVisibleHtml($bodyHtmlTop);
        $hasBodyBottom = RichContent::hasVisibleHtml($bodyHtmlBottom);
        $certificateTitle = trim((string) ($certificateTitle ?? '证书查询'));
        $certificateSummary = trim((string) ($certificateSummary ?? ''));
        $lookupResult = is_array($lookupResult ?? null) ? $lookupResult : null;
        $lookupStatus = (string) ($lookupResult['status'] ?? '');
    @endphp

    <x-breadcrumbs :items="$breadcrumbs ?? []" />

    <section
        data-type="certificateLookup"
        @class([
            'bg-[color:var(--bg-color)] cms-page-content-section cms-special-certificate-lookup-section',
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
            <header class="cms-course-table-section__header cms-category-intro-section__header cms-special-certificate-lookup-section__header text-center max-w-3xl mx-auto">
                <h1 class="font-apex-book cms-section-title text-secondary mb-0">
                    {{ $category->name }}
                </h1>

                @if(filled(strip_tags($introductionHtml ?? '')))
                    <div class="cms-category-intro-section__intro cms-page-content news-rich-text mt-8 text-lg font-din text-primary text-left md:text-center">
                        {!! $introductionHtml !!}
                    </div>
                @endif
            </header>

            @if($hasBodyTop)
                <div class="cms-special-certificate-lookup-section__html-top about-rich-text cms-page-content cms-basic-content__body cms-basic-content__body--html font-din text-[color:var(--ipa-color)]">
                    {!! $bodyHtmlTop !!}
                </div>
            @endif

            <div id="certificate-lookup" class="cms-special-certificate-lookup-section__lookup">
                @include('frontend.partials.certificate-lookup.form', [
                    'category' => $category,
                    'certificateTitle' => $certificateTitle,
                    'certificateSummary' => $certificateSummary,
                    'lookupResult' => $lookupResult,
                ])
            </div>

            @if($hasBodyBottom)
                <div class="cms-special-certificate-lookup-section__html-bottom about-rich-text cms-page-content cms-basic-content__body cms-basic-content__body--html font-din text-[color:var(--ipa-color)]">
                    {!! $bodyHtmlBottom !!}
                </div>
            @endif
        </div>
    </section>
@endsection
