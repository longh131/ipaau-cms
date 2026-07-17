<!doctype html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>@yield('title', '会员中心') - IPA</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/img/monogram_ipa.png') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/fonts.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/home.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/site-fonts.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/member-portal.css') }}" />
    @stack('styles')
    <script src="{{ asset('assets/menu.js') }}" defer></script>
</head>
<body style="--scrollbar-compensation: 0px" class="no-external-icons member-portal {{ $bodyClass ?? '' }}">
    <div id="root">
        @include('partials.header.member-header')

        <main id="main" class="member-portal-main">
            @yield('content')
        </main>

        @include('partials.footer.footer-main')
        @include('partials.footer.site-extras')
    </div>
    @stack('scripts')
</body>
</html>
