document.addEventListener('DOMContentLoaded', function () {
    initNewsViewMore();
    initNewsSearch();
    initArticleJumpMenu();
});

function initNewsViewMore() {
    const btn = document.querySelector('.news-view-more');
    if (!btn) return;
    btn.addEventListener('click', function () {
        document.querySelectorAll('.news-card--hidden').forEach(function (card) {
            card.classList.remove('news-card--hidden', 'hidden');
        });
        btn.closest('.flex').remove();
    });
}

function initNewsSearch() {
    const input = document.getElementById('news-search-input');
    const clear = document.querySelector('.news-search-clear');
    const empty = document.querySelector('.news-search-empty');
    const cards = Array.from(document.querySelectorAll('.news-card'));
    if (!input || cards.length === 0) return;

    const apply = function () {
        const q = input.value.trim().toLowerCase();
        let visible = 0;
        cards.forEach(function (card) {
            const title = card.getAttribute('data-title') || '';
            const match = !q || title.includes(q);
            card.classList.toggle('hidden', !match);
            card.classList.toggle('news-card--filtered-out', !match);
            if (match) visible += 1;
        });
        if (clear) clear.classList.toggle('hidden', !q);
        if (empty) empty.classList.toggle('hidden', visible > 0);
    };

    input.addEventListener('input', apply);
    if (clear) {
        clear.addEventListener('click', function () {
            input.value = '';
            apply();
            input.focus();
        });
    }
}

function initArticleJumpMenu() {
    const links = document.querySelectorAll('.news-jump-link');
    if (links.length === 0) return;

    links.forEach(function (link) {
        link.addEventListener('click', function () {
            const id = link.getAttribute('data-target');
            const target = id ? document.getElementById(id) : null;
            if (target) {
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
            links.forEach(function (l) { l.classList.remove('news-jump-link--active'); });
            link.classList.add('news-jump-link--active');
        });
    });
}
