@extends('layouts.app', [
    'bodyClass' => 'cms-about-page cms-content-page cms-general-secondary-page',
    'headerBlobPartial' => $headerBlobPartial ?? 'blob-about',
])

@section('title', $page->seoTitle())
@section('canonical', route('category.show', $page->slug))
@section('og_title', $page->seoTitle())

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/about-ipa-pages.css') }}" />
@endpush

@push('scripts')
    <script src="{{ asset('assets/js/about-ipa-pages.js') }}" defer></script>
@endpush

@section('content')
    <x-breadcrumbs :items="$breadcrumbs ?? []" />

    @include('frontend.pages.partials.general-secondary-content', [
        'pageView' => $pageView ?? [],
        'hasBreadcrumbs' => ! empty($breadcrumbs ?? []),
    ])
@endsection
