@extends('layouts.app', [
    'bodyClass' => 'cms-about-page cms-content-page',
    'headerBlobPartial' => $headerBlobPartial ?? 'blob-about',
])

@section('title', $page->seoTitle())
@section('canonical', route('category.show', $page->slug))
@section('og_title', $page->seoTitle())

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/about-ipa-pages.css') }}" />
@endpush

@if($pageView['needs_about_scripts'] ?? false)
    @push('scripts')
        <script src="{{ asset('assets/js/about-ipa-pages.js') }}" defer></script>
    @endpush
@endif

@section('content')
    <x-breadcrumbs :items="$breadcrumbs ?? []" />

    @include('frontend.pages.partials.default-content', [
        'page' => $page,
        'pageView' => $pageView ?? [],
        'hasBreadcrumbs' => ! empty($breadcrumbs ?? []),
    ])
@endsection
