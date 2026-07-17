(function () {
    'use strict';

    function getCsrfToken() {
        const meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.getAttribute('content') : '';
    }

    function initSendCode() {
        const button = document.getElementById('send-code-btn');
        const mobileInput = document.getElementById('mobile');
        const message = document.getElementById('send-code-message');

        if (!button || !mobileInput) {
            return;
        }

        let cooldown = 0;
        let timer = null;

        function tick() {
            if (cooldown <= 0) {
                button.disabled = false;
                button.textContent = '获取验证码';
                clearInterval(timer);
                timer = null;
                return;
            }

            button.disabled = true;
            button.textContent = cooldown + 's';
            cooldown -= 1;
        }

        button.addEventListener('click', async function () {
            const mobile = mobileInput.value.trim();

            if (mobile === '') {
                if (message) {
                    message.textContent = '请输入手机号';
                }
                mobileInput.focus();
                return;
            }

            button.disabled = true;

            try {
                const response = await fetch('/member/send-code', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': getCsrfToken(),
                    },
                    body: JSON.stringify({ mobile: mobile }),
                });

                const data = await response.json();

                if (message) {
                    message.textContent = data.message || '';
                }

                if (data.ok) {
                    cooldown = 60;
                    tick();
                    timer = setInterval(tick, 1000);
                } else {
                    button.disabled = false;
                }
            } catch (error) {
                if (message) {
                    message.textContent = '发送失败，请稍后重试';
                }
                button.disabled = false;
            }
        });
    }

    function initUserMenu() {
        const menu = document.querySelector('[data-member-user-menu]');
        const trigger = document.querySelector('[data-member-user-trigger]');
        const dropdown = document.querySelector('[data-member-user-dropdown]');

        if (!menu || !trigger || !dropdown) {
            return;
        }

        function closeMenu() {
            dropdown.hidden = true;
            trigger.setAttribute('aria-expanded', 'false');
        }

        function openMenu() {
            dropdown.hidden = false;
            trigger.setAttribute('aria-expanded', 'true');
        }

        trigger.addEventListener('click', function (event) {
            event.preventDefault();
            event.stopPropagation();

            if (dropdown.hidden) {
                openMenu();
            } else {
                closeMenu();
            }
        });

        dropdown.addEventListener('click', function (event) {
            event.stopPropagation();
        });

        document.addEventListener('click', function (event) {
            if (!menu.contains(event.target)) {
                closeMenu();
            }
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                closeMenu();
            }
        });
    }

    function initProfileTabs() {
        const tabRoot = document.querySelector('[data-member-profile-tabs]');

        if (!tabRoot) {
            return;
        }

        const tabs = tabRoot.querySelectorAll('[data-profile-tab]');
        const panels = document.querySelectorAll('[data-profile-panel]');

        tabs.forEach(function (tab) {
            tab.addEventListener('click', function () {
                const target = tab.getAttribute('data-profile-tab');

                tabs.forEach(function (item) {
                    item.classList.toggle('is-active', item === tab);
                });

                panels.forEach(function (panel) {
                    const isActive = panel.getAttribute('data-profile-panel') === target;
                    panel.hidden = !isActive;
                    panel.classList.toggle('is-active', isActive);
                });
            });
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        initSendCode();
        initUserMenu();
        initProfileTabs();
    });
})();
