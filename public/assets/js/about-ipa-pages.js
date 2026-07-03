document.addEventListener('DOMContentLoaded', function () {
  initAboutTabs();
});

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
