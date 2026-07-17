@extends('layouts.member', ['bodyClass' => 'member-profile-page'])

@section('title', 'My Profile')

@section('content')
    <div class="member-profile-banner">
        <div class="container px-4 md:px-10 mx-auto member-profile-banner__inner">
            <a href="{{ route('member.dashboard') }}" class="member-profile-banner__back">← Back to Dashboard</a>
            <dl class="member-profile-banner__meta">
                <div>
                    <dt>Member ID</dt>
                    <dd>{{ $member->member_number }}</dd>
                </div>
                <div>
                    <dt>Type</dt>
                    <dd>{{ $member->member_level_short ?: $member->member_level ?: '—' }}</dd>
                </div>
                <div>
                    <dt>Member since</dt>
                    <dd>{{ $member->joined_at?->format('d/m/Y') ?: '—' }}</dd>
                </div>
                <div>
                    <dt>Paid through</dt>
                    <dd>{{ $member->level_valid_until?->format('d/m/Y') ?: '—' }}</dd>
                </div>
            </dl>
        </div>
    </div>

    <section class="member-profile container px-4 md:px-10 mx-auto">
        <div class="member-profile__hero">
            <div class="member-profile__hero-main">
                <div class="member-profile__avatar" aria-hidden="true">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                        <path fill-rule="evenodd" d="M7.5 6a4.5 4.5 0 1 1 9 0 4.5 4.5 0 0 1-9 0ZM3.751 20.105a8.25 8.25 0 0 1 16.498 0 .75.75 0 0 1-.437.695A18.683 18.683 0 0 1 12 22.5c-2.786 0-5.433-.608-7.812-1.7a.75.75 0 0 1-.437-.695Z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div>
                    <h1 class="member-profile__name font-apex-book">{{ $member->portal_title }}</h1>
                    <p class="member-profile__subtitle">
                        {{ $member->job_title_zh ?: $member->job_title_en ?: '—' }}
                    </p>
                    <p class="member-profile__subtitle">
                        {{ $member->company_name_zh ?: $member->company_name_en ?: '—' }}
                    </p>
                </div>
            </div>

            @if($expiryNotice = $member->membershipExpiryNotice())
                <div @class([
                    'member-profile__expiry',
                    'member-profile__expiry--expired' => $expiryNotice['status'] === 'expired',
                    'member-profile__expiry--active' => $expiryNotice['status'] === 'active',
                ])>
                    <span class="member-profile__expiry-icon" aria-hidden="true">
                        @if($expiryNotice['status'] === 'expired')
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                                <path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12Zm8.706-1.442c1.146-.573 2.437.463 2.126 1.706l-.709 2.836a1.125 1.125 0 0 0 1.091 1.379h.008c.648 0 1.18-.495 1.243-1.138l.056-.452a1.125 1.125 0 0 0-1.244-1.244l-.452.056a.375.375 0 0 1-.451-.319l-.319-.451A1.125 1.125 0 0 0 11.25 10.5h-.008a1.125 1.125 0 0 0-1.244 1.058Z" clip-rule="evenodd" />
                            </svg>
                        @else
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M4.5 9.75h15M5.25 5.25h13.5a1.5 1.5 0 0 1 1.5 1.5v12.75a1.5 1.5 0 0 1-1.5 1.5H5.25a1.5 1.5 0 0 1-1.5-1.5V6.75a1.5 1.5 0 0 1 1.5-1.5Z" />
                            </svg>
                        @endif
                    </span>
                    <p class="member-profile__expiry-text">{{ $expiryNotice['message'] }}</p>
                </div>
            @endif
        </div>

        <nav class="member-profile-tabs" aria-label="Profile sections" data-member-profile-tabs>
            <button type="button" class="member-profile-tabs__item is-active" data-profile-tab="overview">概览</button>
            <button type="button" class="member-profile-tabs__item" data-profile-tab="about">关于我</button>
            <button type="button" class="member-profile-tabs__item" data-profile-tab="membership">会籍资格</button>
        </nav>

        <div class="member-profile-panel is-active" data-profile-panel="overview">
            <header class="member-profile-panel__header">
                <h2 class="font-apex-book">会籍资格详情</h2>
            </header>

            <dl class="member-detail-grid">
                @foreach($overviewFields as $field)
                    @php
                        $value = $member->{$field};
                        if ($value instanceof \Carbon\Carbon) {
                            $value = $value->format('d/m/Y');
                        }
                        $display = filled($value) ? $value : '—';
                    @endphp
                    <div class="member-detail-grid__item">
                        <dt>{{ $overviewLabels[$field] ?? ($fieldLabels[$field] ?? $field) }}</dt>
                        <dd>{{ $display }}</dd>
                    </div>
                @endforeach
            </dl>
        </div>

        <div class="member-profile-panel" data-profile-panel="about" hidden>
            <header class="member-profile-panel__header">
                <h2 class="font-apex-book">个人信息</h2>
                <span class="member-profile-panel__note">只读展示（编辑功能后续开放）</span>
            </header>

            <dl class="member-detail-grid">
                @foreach($profileFields as $field)
                    @php
                        $value = $member->{$field};
                        if ($value instanceof \Carbon\Carbon) {
                            $value = $value->format('d/m/Y');
                        }
                        $display = filled($value) ? $value : '—';
                    @endphp
                    <div class="member-detail-grid__item">
                        <dt>{{ $fieldLabels[$field] ?? $field }}</dt>
                        <dd>{{ $display }}</dd>
                    </div>
                @endforeach
            </dl>
        </div>

        <div class="member-profile-panel" data-profile-panel="membership" hidden>
            <header class="member-profile-panel__header">
                <h2 class="font-apex-book">会籍资格信息</h2>
            </header>

            <dl class="member-detail-grid">
                @foreach($membershipFields as $field)
                    @php
                        $value = $member->{$field};
                        if ($value instanceof \Carbon\Carbon) {
                            $value = $value->format('d/m/Y');
                        }
                        $display = filled($value) ? $value : '—';
                    @endphp
                    <div class="member-detail-grid__item">
                        <dt>{{ $fieldLabels[$field] ?? $field }}</dt>
                        <dd>{{ $display }}</dd>
                    </div>
                @endforeach
            </dl>
        </div>
    </section>
@endsection

@push('scripts')
    <script src="{{ asset('assets/js/member-portal.js') }}" defer></script>
@endpush
