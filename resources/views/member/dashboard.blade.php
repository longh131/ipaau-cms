@extends('layouts.member', ['bodyClass' => 'member-dashboard-page'])

@section('title', '会员门户')

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
                    ['label' => '会员奖项', 'icon' => 'award', 'url' => url('/category/member-awards')],
                    ['label' => '活动与CPD', 'icon' => 'events-cpd', 'url' => url('/category/events-cpd-preview')],
                    ['label' => '会员资源', 'icon' => 'member-resources', 'url' => url('/category/member-resources')],
                    ['label' => '我的CPD记录', 'icon' => 'cpd', 'url' => url('/category/my-cpd-records')],
                    ['label' => '会籍资格有效证明申请', 'icon' => 'certificate', 'url' => 'https://forms.cloud.microsoft/pages/responsepage.aspx?id=GmIdzLySS06Ym6kNqTWDdopFsLmM2MBMvV8t5wLn4vVUODVBR0pYSlZKMzlDSDlBTjI1MkVETk81RS4u&route=shorturl', 'external' => true],
                    ['label' => '会员中文与商城交易（微信版）', 'icon' => 'shop', 'type' => 'qrcode', 'qrcode' => asset('assets/img/erweima.jpg'), 'qrcode_alt' => 'IPA服务二维码'],
                ] as $tile)
                    @if(($tile['type'] ?? 'link') === 'qrcode')
                        <button
                            type="button"
                            class="member-tile member-tile--link member-tile--qrcode"
                            data-member-qrcode-trigger
                            data-qrcode-src="{{ $tile['qrcode'] }}"
                            data-qrcode-alt="{{ $tile['qrcode_alt'] ?? $tile['label'] }}"
                            aria-haspopup="dialog"
                        >
                            <span class="member-tile__icon" aria-hidden="true">
                                @include('member.partials.tile-icon', ['name' => $tile['icon']])
                            </span>
                            <span class="member-tile__label">{{ $tile['label'] }}</span>
                        </button>
                    @else
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
                    @endif
                @endforeach
            </div>

            <div class="member-dashboard__bottom">
                @foreach([
                    ['label' => 'My Portal', 'subtitle' => '全球官网', 'icon' => 'portal', 'url' => 'https://www.publicaccountants.org.au/', 'external' => true],
                    ['label' => '全球活动', 'subtitle' => null, 'icon' => 'events', 'url' => 'https://www.publicaccountants.org.au/education-events/events/', 'external' => true],
                    ['label' => '我的社区', 'icon' => 'community', 'url' => url('/category/my-community')],
                    ['label' => '会籍资格升级', 'icon' => 'member-levels', 'url' => url('/category/member-levels')],
                ] as $index => $tile)
                    <a
                        href="{{ $tile['url'] }}"
                        @class([
                            'member-tile',
                            'member-tile--link',
                            'member-tile--portal' => $index === 0,
                        ])
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
        </div>
    </section>
@endsection

@push('overlays')
    <div class="member-qrcode-modal" data-member-qrcode-modal aria-hidden="true">
        <div class="member-qrcode-modal__backdrop" data-member-qrcode-close tabindex="-1"></div>
        <div class="member-qrcode-modal__dialog" role="dialog" aria-modal="true" aria-labelledby="member-qrcode-modal-title">
            <h2 id="member-qrcode-modal-title" class="member-qrcode-modal__title font-apex-book">IPA服务</h2>
            <button type="button" class="member-qrcode-modal__close" data-member-qrcode-close aria-label="关闭">&times;</button>
            <img
                src="{{ asset('assets/img/erweima.jpg') }}"
                alt="IPA服务二维码"
                class="member-qrcode-modal__image"
                data-member-qrcode-image
            />
        </div>
    </div>
@endpush

@push('scripts')
    <script src="{{ asset('assets/js/member-portal.js') }}" defer></script>
@endpush
