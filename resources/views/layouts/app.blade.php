<!doctype html>
<html lang="{{ $htmlLang ?? 'en' }}">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title', 'Institute of Public Accountants')</title>
    @hasSection('canonical')
        <link rel="canonical" href="@yield('canonical')" />
    @endif
    @hasSection('og_title')
        <meta property="og:title" content="@yield('og_title')" />
    @endif
    <link rel="icon" type="image/png" href="{{ asset('assets/img/monogram_ipa.png') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/fonts.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/home.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/site-fonts.css') }}" />
    @stack('styles')
    @hasSection('json_ld')
        @yield('json_ld')
    @endif
    <script src="{{ asset('assets/menu.js') }}" defer></script>
    @stack('head')
</head>
<body style="--scrollbar-compensation: 0px" class="no-external-icons {{ $bodyClass ?? '' }}">
    <div id="root">
        <a
            class="h-xl z-50 w-auto p-2 absolute top-0 -translate-y-full focus-visible:translate-y-0 transition-all duration-300 bg-secondary text-white"
            href="#main"
        >Skip to main content</a>

        @include('partials.header.site-header')

        <main id="main">
            @yield('content')
        </main>

        @include('partials.footer.footer-main')
        @include('partials.footer.site-extras')
    </div>
    @stack('scripts')
    <script src="{{ asset('assets/js/home.js') }}" defer></script>
</body>
</html>
