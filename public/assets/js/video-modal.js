document.addEventListener('DOMContentLoaded', function () {
    var modal = document.querySelector('[data-video-modal]');
    if (!modal) {
        return;
    }

    var video = modal.querySelector('.cms-video-modal__video');
    var source = video ? video.querySelector('source') : null;
    var titleEl = modal.querySelector('#cms-video-modal-title');
    var openers = document.querySelectorAll('[data-video-modal-open]');
    var closers = modal.querySelectorAll('[data-video-modal-close]');

    if (!video || !source || openers.length === 0) {
        return;
    }

    var closeModal = function () {
        video.pause();
        video.removeAttribute('src');
        source.setAttribute('src', '');
        video.load();
        modal.classList.remove('is-open');
        modal.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('cms-video-modal-open');
    };

    var openModal = function (trigger) {
        var src = trigger.getAttribute('data-video-src') || '';
        if (!src) {
            return;
        }

        var type = trigger.getAttribute('data-video-type') || 'video/mp4';
        var poster = trigger.getAttribute('data-video-poster') || '';
        var title = trigger.getAttribute('data-video-title') || '视频播放';

        if (titleEl) {
            titleEl.textContent = title;
        }

        source.setAttribute('src', src);
        source.setAttribute('type', type);
        if (poster) {
            video.setAttribute('poster', poster);
        } else {
            video.removeAttribute('poster');
        }
        video.load();
        modal.classList.add('is-open');
        modal.setAttribute('aria-hidden', 'false');
        document.body.classList.add('cms-video-modal-open');
        var playPromise = video.play();
        if (playPromise && typeof playPromise.catch === 'function') {
            playPromise.catch(function () {});
        }
    };

    openers.forEach(function (trigger) {
        trigger.addEventListener('click', function () {
            openModal(trigger);
        });
    });

    closers.forEach(function (el) {
        el.addEventListener('click', closeModal);
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape' && modal.classList.contains('is-open')) {
            closeModal();
        }
    });
});
