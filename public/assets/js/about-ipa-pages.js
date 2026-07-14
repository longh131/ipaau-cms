document.addEventListener('DOMContentLoaded', function () {
  initAboutTabs();
  initNewsListCurated();
  initNewsletterForm();
});

function initNewsletterForm() {
  document.querySelectorAll('form[data-newsletter-form]').forEach(function (form) {
    var feedback = form.querySelector('[data-newsletter-feedback]');
    var submitBtn = form.querySelector('button[type="submit"]');
    var defaultButtonHtml = submitBtn ? submitBtn.innerHTML : '';

    form.addEventListener('submit', async function (e) {
      e.preventDefault();

      if (!submitBtn) {
        return;
      }

      if (feedback) {
        feedback.classList.add('hidden');
        feedback.textContent = '';
      }

      submitBtn.disabled = true;

      var csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
      var formData = new FormData(form);

      try {
        var response = await fetch(form.action, {
          method: 'POST',
          headers: {
            Accept: 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'X-Requested-With': 'XMLHttpRequest',
          },
          body: formData,
        });

        var payload = await response.json().catch(function () {
          return {};
        });

        if (!response.ok) {
          var message = payload.message
            || (payload.errors ? Object.values(payload.errors).flat().join(' ') : '提交失败，请稍后重试。');
          throw new Error(message);
        }

        if (feedback) {
          feedback.textContent = payload.message || '提交成功，感谢您的订阅。';
          feedback.classList.remove('hidden');
          feedback.classList.remove('bg-red-50', 'text-red-700', 'border', 'border-red-200');
          feedback.classList.add('bg-green-50', 'text-green-800', 'border', 'border-green-200');
        }

        form.reset();
      } catch (error) {
        if (feedback) {
          feedback.textContent = error.message || '提交失败，请稍后重试。';
          feedback.classList.remove('hidden');
          feedback.classList.remove('bg-green-50', 'text-green-800', 'border', 'border-green-200');
          feedback.classList.add('bg-red-50', 'text-red-700', 'border', 'border-red-200');
        }
      } finally {
        submitBtn.disabled = false;
        submitBtn.innerHTML = defaultButtonHtml;
      }
    });
  });
}

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
