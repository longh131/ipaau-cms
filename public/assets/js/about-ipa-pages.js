document.addEventListener('DOMContentLoaded', function () {
  initAboutTabs();
  initAboutTestimonials();
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

function initAboutTestimonials() {
  document.querySelectorAll('.about-testimonial-carousel').forEach(function (carousel) {
    const slides = carousel.querySelectorAll('.about-testimonial-slide');
    const dots = carousel.querySelectorAll('.about-testimonial-dot');
    if (!slides.length) return;

    let current = 0;
    let timer = null;

    function goTo(index) {
      if (index < 0 || index >= slides.length) return;
      current = index;
      slides.forEach(function (slide, i) {
        slide.classList.toggle('about-testimonial-slide--active', i === current);
      });
      dots.forEach(function (dot, i) {
        dot.classList.toggle('about-testimonial-dot--active', i === current);
      });
    }

    dots.forEach(function (dot, index) {
      dot.addEventListener('click', function () {
        goTo(index);
        resetAutoplay();
      });
    });

    function resetAutoplay() {
      if (timer) clearInterval(timer);
      if (slides.length < 2) return;
      timer = setInterval(function () {
        goTo((current + 1) % slides.length);
      }, 7000);
    }

    resetAutoplay();
  });
}
