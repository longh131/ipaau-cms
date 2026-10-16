@php
    $siteSeo = app(\App\Services\SiteSettingsService::class);
    $documentTitle = $siteSeo->documentTitle($__env->yieldContent('title'));
    $metaDescription = $siteSeo->metaText($__env->yieldContent('meta_description'));

    if ($metaDescription === '') {
        $metaDescription = $siteSeo->getSeoDescription();
    }

    $metaKeywords = $siteSeo->metaText($__env->yieldContent('meta_keywords'));

    if ($metaKeywords === '') {
        $metaKeywords = $siteSeo->getSeoKeywords();
    }
@endphp
<title>{{ $documentTitle }}</title>
@if ($metaDescription !== '')
    <meta name="description" content="{{ $metaDescription }}" />
@endif
@if ($metaKeywords !== '')
    <meta name="keywords" content="{{ $metaKeywords }}" />
@endif
<meta property="og:title" content="{{ $documentTitle }}" />
