@extends('layouts.member', ['bodyClass' => 'member-dashboard-page'])

@section('title', 'Dashboard')

@section('content')
    @if($member->isMembershipExpired())
        <div class="member-status-banner member-status-banner--warning">
            <span>Membership expired on {{ $member->level_valid_until?->format('n月j日') }}</span>
        </div>
    @endif

    <section class="member-dashboard container px-4 md:px-10 mx-auto">
        <div class="member-dashboard__grid">
            <aside class="member-profile-card">
                <div class="member-profile-card__avatar" aria-hidden="true">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                        <path fill-rule="evenodd" d="M7.5 6a4.5 4.5 0 1 1 9 0 4.5 4.5 0 0 1-9 0ZM3.751 20.105a8.25 8.25 0 0 1 16.498 0 .75.75 0 0 1-.437.695A18.683 18.683 0 0 1 12 22.5c-2.786 0-5.433-.608-7.812-1.7a.75.75 0 0 1-.437-.695Z" clip-rule="evenodd" />
                    </svg>
                </div>
                <h1 class="member-profile-card__name font-apex-book">{{ $member->portal_title }}</h1>
                <a href="{{ route('member.profile') }}" class="member-profile-card__link">我的信息</a>
            </aside>

            <div class="member-dashboard__tiles">
                @foreach([
                    ['label' => '会员奖项', 'icon' => 'award', 'url' => '#'],
                    ['label' => '活动与CPD', 'icon' => 'events-cpd', 'url' => url('/category/events-cpd')],
                    ['label' => '会员资源', 'icon' => 'member-resources', 'url' => url('/category/member-resources')],
                    ['label' => '我的CPD记录', 'icon' => 'cpd', 'url' => '#'],
                    ['label' => '会籍资格有效证明申请', 'icon' => 'certificate', 'url' => 'https://forms.office.com/pages/responsepage.aspx?id=GmIdzLySS06Ym6kNqTWDdopFsLmM2MBMvV8t5wLn4vVUODVBR0pYSlZKMzlDSDlBTjI1MkVETk81RS4u&route=shorturl', 'external' => true],
                    ['label' => '会员中心与商城交易（微信版）', 'icon' => 'shop', 'url' => asset('assets/files/会员中心与商城交易（微信版）.pdf'), 'external' => true],
                ] as $tile)
                    <a
                        href="{{ $tile['url'] }}"
                        @class(['member-tile', 'member-tile--link'])
                        @if($tile['external'] ?? false) target="_blank" rel="noopener noreferrer" @endif
                    >
                        <span class="member-tile__icon" aria-hidden="true">
                            @include('member.partials.tile-icon', ['name' => $tile['icon']])
                        </span>
                        <span class="member-tile__label">{{ $tile['label'] }}</span>
                    </a>
                @endforeach
            </div>
        </div>

        <div class="member-dashboard__bottom-tiles">
            @foreach([
                ['label' => 'My Portal', 'subtitle' => '全球官网', 'icon' => 'portal', 'url' => 'https://www.publicaccountants.org.au/', 'external' => true],
                ['label' => '全球活动', 'subtitle' => null, 'icon' => 'events', 'url' => '#'],
                ['label' => 'MyCommunity', 'icon' => 'community', 'url' => '#'],
                ['label' => '会籍资格升级', 'icon' => 'member-levels', 'url' => url('/category/member-levels')],
            ] as $tile)
                <a
                    href="{{ $tile['url'] }}"
                    @class(['member-tile', 'member-tile--wide', 'member-tile--link'])
                    @if($tile['external'] ?? false) target="_blank" rel="noopener noreferrer" @endif
                >
                    <span class="member-tile__icon" aria-hidden="true">
                        @include('member.partials.tile-icon', ['name' => $tile['icon']])
                    </span>
                    <span class="member-tile__label">{{ $tile['label'] }}</span>
                    @if(filled($tile['subtitle'] ?? null))
                        <span class="member-tile__subtitle">{{ $tile['subtitle'] }}</span>
                    @endif
                </a>
            @endforeach
        </div>
    </section>
@endsection

@push('scripts')
    <script src="{{ asset('assets/js/member-portal.js') }}" defer></script>
@endpush
