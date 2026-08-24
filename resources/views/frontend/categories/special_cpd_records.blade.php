@extends('layouts.app', [
    'bodyClass' => 'cms-about-page cms-content-page cms-special-cpd-records-page',
    'headerBlobPartial' => 'blob-about',
])

@section('title', $category->name)
@section('canonical', route('category.show', $category->slug))
@section('og_title', $category->name)

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/about-ipa-pages.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/news-pages.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/special-category-pages.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/cpd-records.css') }}" />
@endpush

@section('content')
    @php
        use App\Support\RichContent;

        $bodyHtmlTop = (string) ($bodyHtmlTop ?? '');
        $bodyHtmlBottom = (string) ($bodyHtmlBottom ?? '');
        $hasBodyTop = RichContent::hasVisibleHtml($bodyHtmlTop);
        $hasBodyBottom = RichContent::hasVisibleHtml($bodyHtmlBottom);
        $loggedInMember = $loggedInMember ?? null;
        $cpdSearchFrom = (string) ($cpdSearchFrom ?? '');
        $cpdSearchTo = (string) ($cpdSearchTo ?? '');
        $cpdSearchResult = is_array($cpdSearchResult ?? null) ? $cpdSearchResult : null;
        $hasSearchResult = $cpdSearchResult !== null;
        $sessionCount = (int) ($cpdSearchResult['session_count'] ?? 0);
        $totalCredits = (float) ($cpdSearchResult['total_credits'] ?? 0);
    @endphp

    <x-breadcrumbs :items="$breadcrumbs ?? []" />

    <section
        data-type="cpdRecords"
        @class([
            'bg-[color:var(--bg-color)] cms-page-content-section cms-special-category-page-section cms-special-cpd-records-section',
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
            <header class="cms-course-table-section__header cms-category-intro-section__header cms-special-category-page-section__header text-center max-w-3xl mx-auto">
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
                <div class="cms-special-cpd-records-section__html-top about-rich-text cms-page-content cms-basic-content__body cms-basic-content__body--html font-din text-[color:var(--ipa-color)]">
                    {!! $bodyHtmlTop !!}
                </div>
            @endif

            <div id="cpd-records-search" class="cms-cpd-records-search">
                <div class="cms-cpd-records-search__panel">
                    <h2 class="cms-cpd-records-search__title font-apex-book">查询统计</h2>
                    <p class="cms-cpd-records-search__intro font-din">
                        在指定时间段内，统计该会员参加的 CPD 活动场次（线下 + 在线课程，仅统计「到场」记录）。
                    </p>

                    @if($errors->has('cpd_search'))
                        <div class="cms-cpd-records-search__alert" role="alert">
                            {{ $errors->first('cpd_search') }}
                        </div>
                    @endif

                    @if($loggedInMember === null)
                        <div class="cms-cpd-records-search__login">
                            <p class="font-din">请先登录会员账户后再查询 CPD 记录。</p>
                            <a
                                href="{{ route('member.login', ['redirect' => route('category.show', $category->slug)]) }}"
                                class="cms-cpd-records-search__login-link"
                            >
                                请登录
                            </a>
                        </div>
                    @else
                        <form method="POST" action="{{ route('member.cpd-records.search') }}" class="cms-cpd-records-search__form font-din">
                            @csrf

                            <p class="cms-cpd-records-search__label">请选择您要查询的学分证明期间:</p>

                            <div class="cms-cpd-records-search__fields">
                                <label class="cms-cpd-records-search__field">
                                    <span>From</span>
                                    <input
                                        type="date"
                                        name="from"
                                        value="{{ old('from', $cpdSearchFrom) }}"
                                        required
                                    />
                                </label>

                                <label class="cms-cpd-records-search__field">
                                    <span>To</span>
                                    <input
                                        type="date"
                                        name="to"
                                        value="{{ old('to', $cpdSearchTo) }}"
                                        required
                                    />
                                </label>
                            </div>

                            <button type="submit" class="cms-cpd-records-search__submit">
                                搜索
                            </button>
                        </form>

                        @if($hasSearchResult)
                            <div class="cms-cpd-records-search__result" aria-live="polite">
                                @if($sessionCount > 0)
                                    <p class="cms-cpd-records-search__summary">
                                        共 <strong>{{ $sessionCount }}</strong> 场 CPD 活动，
                                        合计 <strong>{{ rtrim(rtrim(number_format($totalCredits, 1, '.', ''), '0'), '.') }}</strong> 学分。
                                    </p>

                                    <a
                                        href="{{ route('member.cpd-records.print', ['from' => $cpdSearchFrom, 'to' => $cpdSearchTo]) }}"
                                        class="cms-cpd-records-search__print-link"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                    >
                                        查询或打印学分记录
                                    </a>
                                @else
                                    <p class="cms-cpd-records-search__summary cms-cpd-records-search__summary--empty">
                                        所选期间内暂无到场 CPD 活动记录。
                                    </p>
                                @endif
                            </div>
                        @endif
                    @endif
                </div>
            </div>

            @if($hasBodyBottom)
                <div class="cms-special-cpd-records-section__html-bottom about-rich-text cms-page-content cms-basic-content__body cms-basic-content__body--html font-din text-[color:var(--ipa-color)]">
                    {!! $bodyHtmlBottom !!}
                </div>
            @endif
        </div>

        @include('frontend.pages.partials.page-content-footer-spacer')
    </section>
@endsection
