{{-- 右下角客服：图片与链接后续可在此替换 --}}
@php
    $csCollapsedImage = asset('assets/img/customer-service-down.png');
    $csExpandedImage = asset('assets/img/customer-service-up.jpg');
    $csFormUrl = 'https://forms.cloud.microsoft/r/C1dLktYKEX';
@endphp

<link rel="stylesheet" href="{{ asset('assets/css/customer-service.css') }}" />

<div
    class="cms-cs-widget"
    data-cs-widget
    data-cs-scroll-threshold="80"
>
    <button
        type="button"
        class="cms-cs-widget__launcher"
        data-cs-open
        aria-label="打开网站问题反馈"
        aria-expanded="false"
    >
        <img src="{{ $csCollapsedImage }}" alt="网站问题反馈" width="72" height="72" />
    </button>

    <div class="cms-cs-widget__panel" data-cs-panel hidden>
        <button
            type="button"
            class="cms-cs-widget__collapse"
            data-cs-close
            aria-label="收起网站问题反馈"
        >
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd" d="M12.53 16.28a.75.75 0 0 1-1.06 0l-7.5-7.5a.75.75 0 0 1 1.06-1.06L12 14.69l6.97-6.97a.75.75 0 1 1 1.06 1.06l-7.5 7.5Z" clip-rule="evenodd" />
            </svg>
        </button>
        <a
            class="cms-cs-widget__panel-link"
            href="{{ $csFormUrl }}"
            target="_blank"
            rel="noopener noreferrer"
        >
            <img src="{{ $csExpandedImage }}" alt="扫描二维码或点击此处填写网站问题反馈" />
        </a>
    </div>
</div>

<script>
(function () {
    var widget = document.querySelector('[data-cs-widget]');
    if (!widget || widget.dataset.bound === '1') {
        return;
    }
    widget.dataset.bound = '1';

    var openButton = widget.querySelector('[data-cs-open]');
    var closeButton = widget.querySelector('[data-cs-close]');
    var panel = widget.querySelector('[data-cs-panel]');
    var threshold = parseInt(widget.getAttribute('data-cs-scroll-threshold') || '80', 10);

    var setOpen = function (open) {
        widget.classList.toggle('is-open', open);
        if (panel) {
            panel.hidden = !open;
        }
        if (openButton) {
            openButton.setAttribute('aria-expanded', open ? 'true' : 'false');
        }
        if (open) {
            widget.classList.add('is-visible');
        } else {
            syncVisibility();
        }
    };

    var syncVisibility = function () {
        if (widget.classList.contains('is-open')) {
            widget.classList.add('is-visible');
            return;
        }
        widget.classList.toggle('is-visible', window.scrollY > threshold);
    };

    if (openButton) {
        openButton.addEventListener('click', function () {
            setOpen(true);
        });
    }

    if (closeButton) {
        closeButton.addEventListener('click', function (event) {
            event.preventDefault();
            event.stopPropagation();
            setOpen(false);
        });
    }

    window.addEventListener('scroll', syncVisibility, { passive: true });
    window.addEventListener('resize', syncVisibility);
    syncVisibility();
})();
</script>
