(function () {
    'use strict';

    function getCsrfToken() {
        const meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.getAttribute('content') : '';
    }

    function syncCsrfToken(form) {
        if (!form) {
            return;
        }

        const meta = document.querySelector('meta[name="csrf-token"]');
        const tokenInput = form.querySelector('input[name="_token"]');

        if (meta && tokenInput) {
            tokenInput.value = meta.getAttribute('content') || '';
        }
    }

    function initLoginForm() {
        const form = document.getElementById('member-login-form');
        const message = document.getElementById('send-code-message');
        const errorBox = document.querySelector('.member-alert--error');

        if (!form) {
            return;
        }

        form.addEventListener('submit', async function (event) {
            event.preventDefault();
            syncCsrfToken(form);

            const submitButton = form.querySelector('[type="submit"]');

            if (submitButton) {
                submitButton.disabled = true;
            }

            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': getCsrfToken(),
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: new FormData(form),
                });

                const data = await response.json().catch(function () {
                    return null;
                });

                if (response.status === 419) {
                    if (message) {
                        message.textContent = '页面已过期，请刷新后重试。';
                    }
                    return;
                }

                if (response.ok && data && data.ok && data.redirect) {
                    window.location.assign(data.redirect);
                    return;
                }

                const errorMessage = (data && (data.message || Object.values(data.errors || {})[0])) || '登录失败，请重试。';

                if (errorBox) {
                    errorBox.textContent = errorMessage;
                    errorBox.hidden = false;
                } else if (message) {
                    message.textContent = errorMessage;
                }
            } catch (error) {
                if (message) {
                    message.textContent = '登录失败，请稍后重试。';
                }
            } finally {
                if (submitButton) {
                    submitButton.disabled = false;
                }
            }
        });
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
                syncCsrfToken(document.getElementById('member-login-form'));

                const response = await fetch('/member/send-code', {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': getCsrfToken(),
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify({ mobile: mobile }),
                });

                const data = await response.json();

                if (message) {
                    message.textContent = data.message || '';
                }

                if (response.status === 419) {
                    if (message) {
                        message.textContent = '页面已过期，请刷新后重试。';
                    }
                    button.disabled = false;
                    return;
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

    function initQrcodeModal() {
        const modal = document.querySelector('[data-member-qrcode-modal]');
        const image = document.querySelector('[data-member-qrcode-image]');
        const triggers = document.querySelectorAll('[data-member-qrcode-trigger]');

        if (!modal || !image || triggers.length === 0) {
            return;
        }

        const closeElements = modal.querySelectorAll('[data-member-qrcode-close]');
        let lastTrigger = null;

        function closeModal() {
            modal.classList.remove('is-open');
            modal.setAttribute('aria-hidden', 'true');
            document.body.classList.remove('member-qrcode-modal-open');

            if (lastTrigger) {
                lastTrigger.focus();
            }
        }

        function openModal(trigger) {
            const src = trigger.getAttribute('data-qrcode-src') || image.getAttribute('src');
            const alt = trigger.getAttribute('data-qrcode-alt') || '二维码';

            if (!src) {
                return;
            }

            lastTrigger = trigger;
            image.src = src;
            image.alt = alt;
            modal.classList.add('is-open');
            modal.setAttribute('aria-hidden', 'false');
            document.body.classList.add('member-qrcode-modal-open');
            modal.querySelector('.member-qrcode-modal__close')?.focus();
        }

        triggers.forEach(function (trigger) {
            trigger.addEventListener('click', function () {
                openModal(trigger);
            });
        });

        closeElements.forEach(function (element) {
            element.addEventListener('click', closeModal);
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape' && modal.classList.contains('is-open')) {
                closeModal();
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
        initLoginForm();
        initSendCode();
        initUserMenu();
        initQrcodeModal();
        initProfileTabs();
    });
})();
