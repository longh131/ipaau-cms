@extends('layouts.app', [
    'bodyClass' => 'cms-about-page cms-content-page cms-video-list-page cms-special-video-hub-page',
    'headerBlobPartial' => 'blob-about',
])

@section('title', $category->name)
@section('canonical', route('category.show', $category->slug))
@section('og_title', $category->name)

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/about-ipa-pages.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/news-pages.css') }}" />
@endpush

@section('content')
    @php
        use App\Support\RichContent;

        $bodyHtmlTop = (string) ($bodyHtmlTop ?? '');
        $bodyHtmlBottom = (string) ($bodyHtmlBottom ?? '');
        $hasBodyTop = RichContent::hasVisibleHtml($bodyHtmlTop);
        $hasBodyBottom = RichContent::hasVisibleHtml($bodyHtmlBottom);
        $videoSections = $videoSections ?? [];
    @endphp

    <x-breadcrumbs :items="$breadcrumbs ?? []" />

    <section
        data-type="videoHub"
        @class([
            'bg-[color:var(--bg-color)] cms-page-content-section cms-special-video-hub-section',
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
            <header class="cms-category-intro-section__header cms-special-video-hub-section__header text-center max-w-3xl mx-auto">
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
                <div class="cms-special-video-hub-section__html-top about-rich-text cms-page-content cms-basic-content__body cms-basic-content__body--html font-din text-[color:var(--ipa-color)] mt-12 lg:mt-16">
                    {!! $bodyHtmlTop !!}
                </div>
            @endif

            <div class="cms-special-video-hub-section__blocks flex flex-col gap-16 lg:gap-20 mt-12 lg:mt-16">
                @foreach ($videoSections as $section)
                    @include('frontend.partials.video-hub.section', ['section' => $section])
                @endforeach
            </div>

            @if($hasBodyBottom)
                <div class="cms-special-video-hub-section__html-bottom about-rich-text cms-page-content cms-basic-content__body cms-basic-content__body--html font-din text-[color:var(--ipa-color)] mt-16 lg:mt-20">
                    {!! $bodyHtmlBottom !!}
                </div>
            @endif
        </div>
    </section>
@endsection

@push('scripts')
    <script src="{{ asset('assets/js/news-pages.js') }}" defer></script>
@endpush
