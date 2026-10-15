@php
    $portalMember = session('ipa_member_id')
        ? \App\Models\IpaMember::query()->find(session('ipa_member_id'))
        : null;
    $memberDisplayName = $portalMember
        ? (trim((string) ($portalMember->full_name ?: $portalMember->display_name)) ?: $portalMember->member_number)
        : '';
    $memberLevelLabel = $portalMember
        ? trim((string) ($portalMember->member_level_short ?: $portalMember->member_level))
        : '';
@endphp

@if($portalMember)
    <div class="member-user-menu relative" data-member-user-menu>
        <button
            type="button"
            class="member-user-menu__trigger"
            aria-expanded="false"
            aria-haspopup="true"
            aria-label="会员账户菜单"
            data-member-user-trigger
        >
            <span class="member-user-menu__avatar" aria-hidden="true">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-5 w-5">
                    <path fill-rule="evenodd" d="M7.5 6a4.5 4.5 0 1 1 9 0 4.5 4.5 0 0 1-9 0ZM3.751 20.105a8.25 8.25 0 0 1 16.498 0 .75.75 0 0 1-.437.695A18.683 18.683 0 0 1 12 22.5c-2.786 0-5.433-.608-7.812-1.7a.75.75 0 0 1-.437-.695Z" clip-rule="evenodd" />
                </svg>
            </span>
            <span class="member-user-menu__chevron" aria-hidden="true">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-4 w-4">
                    <path fill-rule="evenodd" d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                </svg>
            </span>
        </button>
        <div class="member-user-menu__dropdown" data-member-user-dropdown hidden>
            <div class="member-user-menu__identity">
                <p class="member-user-menu__name">{{ $memberDisplayName }}</p>
                @if($memberLevelLabel !== '')
                    <p class="member-user-menu__level">{{ $memberLevelLabel }}</p>
                @endif
            </div>
            <a href="{{ route('member.dashboard') }}">会员门户</a>
            <form method="POST" action="{{ route('member.logout') }}">
                @csrf
                <button type="submit">退出登录</button>
            </form>
        </div>
    </div>
@else
    <a
        href="{{ route('member.login') }}"
        class="cta group font-medium uppercase border-2 border-link bg-transparent text-link hover:bg-link-hover hover:text-white focus-visible:bg-link-hover focus-visible:text-white focus-visible:border-link-focused focus-visible:outline-[4px] focus-visible:outline-link-focused focus-visible:outline focus-visible:no-underline disabled:bg-disabled disabled:border-grey disabled:text-grey disabled:hover:no-underline disabled:cursor-not-allowed flex transition-all duration-300 border uppercase text-lg hover:underline focus-visible:underline p-[9px] rounded-full"
        tabindex="0"
    >
        <div class="flex flex-wrap items-center w-full">
            <div class="cta-content flex flex-nowrap items-center justify-center w-full uppercase">
                Sign In
            </div>
        </div>
    </a>
@endif

@once
    <script>
        document.addEventListener('click', function (event) {
            var menus = document.querySelectorAll('[data-member-user-menu]');
            var trigger = event.target.closest ? event.target.closest('[data-member-user-trigger]') : null;

            function closeMenu(menu) {
                var panel = menu.querySelector('[data-member-user-dropdown]');
                var button = menu.querySelector('[data-member-user-trigger]');
                if (panel) {
                    panel.hidden = true;
                }
                if (button) {
                    button.setAttribute('aria-expanded', 'false');
                }
                menu.classList.remove('is-open');
            }

            if (trigger) {
                event.preventDefault();
                var menu = trigger.closest('[data-member-user-menu]');
                var panel = menu ? menu.querySelector('[data-member-user-dropdown]') : null;
                var willOpen = panel ? panel.hidden : false;

                menus.forEach(closeMenu);

                if (willOpen && menu && panel) {
                    panel.hidden = false;
                    trigger.setAttribute('aria-expanded', 'true');
                    menu.classList.add('is-open');
                }

                return;
            }

            menus.forEach(function (menu) {
                if (!menu.contains(event.target)) {
                    closeMenu(menu);
                }
            });
        });

        document.addEventListener('keydown', function (event) {
            if (event.key !== 'Escape') {
                return;
            }

            document.querySelectorAll('[data-member-user-menu]').forEach(function (menu) {
                var panel = menu.querySelector('[data-member-user-dropdown]');
                var button = menu.querySelector('[data-member-user-trigger]');
                if (panel) {
                    panel.hidden = true;
                }
                if (button) {
                    button.setAttribute('aria-expanded', 'false');
                }
                menu.classList.remove('is-open');
            });
        });
    </script>
@endonce
