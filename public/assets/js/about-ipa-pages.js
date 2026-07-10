document.addEventListener('DOMContentLoaded', function () {
  initAboutTabs();
  initNewsListCurated();
});

function initNewsListCurated() {
  document.querySelectorAll('.cms-news-list-curated--expandable').forEach(function (section) {
    var toggle = section.querySelector('.cms-news-list-curated__toggle');
    if (!toggle || toggle.dataset.bound === 'true') {
      return;
    }

    toggle.dataset.bound = 'true';

    toggle.addEventListener('click', function (event) {
      event.preventDefault();

      section.querySelectorAll('.cms-news-list-curated__item--hidden').forEach(function (item) {
        item.classList.remove('cms-news-list-curated__item--hidden');
      });

      section.classList.add('is-expanded');
      toggle.setAttribute('aria-expanded', 'true');

      var actions = section.querySelector('.cms-news-list-curated__actions');
      if (actions) {
        actions.hidden = true;
      }
    });
  });
}

function initAboutTabs() {
  document.querySelectorAll('section[data-type="tabbedContent"].about-tabbed-section').forEach(function (section) {
    const tabGroups = section.querySelectorAll('.about-tab-list');
    const panels = section.querySelectorAll('[data-type="tab-content"]');
    const images = section.querySelectorAll('.about-tab-images img');
    if (!tabGroups.length || !panels.length) return;

    function switchTab(index) {
      tabGroups.forEach(function (group) {
        group.querySelectorAll('.tab').forEach(function (tab, i) {
          tab.classList.toggle('active', i === index);
        });
      });
      panels.forEach(function (panel, i) {
        panel.classList.toggle('hidden', i !== index);
      });
      images.forEach(function (img, i) {
        img.classList.toggle('is-active', i === index);
      });
    }

    tabGroups.forEach(function (group) {
      group.querySelectorAll('.tab').forEach(function (tab, index) {
        tab.addEventListener('click', function (e) {
          e.preventDefault();
          switchTab(index);
        });
      });
    });

    switchTab(0);
  });
}
