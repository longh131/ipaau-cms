// =========================
// IPA HOME PAGE JS
// =========================

document.addEventListener("DOMContentLoaded", function () {

    // =========================
    // 1. MOBILE MENU TOGGLE
    // =========================

    const menuBtn = document.getElementById("menuBtn");
    const mobileMenu = document.getElementById("mobileMenu");
    const closeMenu = document.getElementById("closeMenu");

    if (menuBtn && mobileMenu) {
        menuBtn.addEventListener("click", function () {
            mobileMenu.classList.add("open");
            mobileMenu.classList.remove("hidden");
        });
    }

    if (closeMenu && mobileMenu) {
        closeMenu.addEventListener("click", function () {
            mobileMenu.classList.remove("open");
            mobileMenu.classList.add("hidden");
        });
    }

    // =========================
    // 2. DROPDOWN HOVER (DESKTOP NAV)
    // =========================

    const dropdowns = document.querySelectorAll(".dropdown");

    dropdowns.forEach(function (item) {
        item.addEventListener("mouseenter", function () {
            const menu = this.querySelector(".dropdown-menu");
            if (menu) menu.style.display = "block";
        });

        item.addEventListener("mouseleave", function () {
            const menu = this.querySelector(".dropdown-menu");
            if (menu) menu.style.display = "none";
        });
    });

    // =========================
    // 3. SIMPLE CAROUSEL (TESTIMONIALS)
    // =========================

    const slides = document.querySelectorAll(".slide");
    const dots = document.querySelectorAll(".dot");

    let currentIndex = 0;

    function showSlide(index) {

        slides.forEach((s, i) => {
            s.style.display = (i === index) ? "block" : "none";
        });

        dots.forEach((d, i) => {
            d.classList.toggle("active", i === index);
        });

        currentIndex = index;
    }

    if (slides.length > 0) {
        showSlide(0);
    }

    dots.forEach((dot, index) => {
        dot.addEventListener("click", function () {
            showSlide(index);
        });
    });

    setInterval(function () {
        if (slides.length > 0) {
            let next = (currentIndex + 1) % slides.length;
            showSlide(next);
        }
    }, 5000);

    // =========================
    // 4. ADA / CHAT WIDGET HOOK (if exists)
    // =========================

    const adaBtn = document.querySelector(".ada-button");
    const adaPanel = document.querySelector(".ada-panel");

    if (adaBtn && adaPanel) {
        adaBtn.addEventListener("click", function () {
            adaPanel.classList.toggle("show");
        });
    }

});