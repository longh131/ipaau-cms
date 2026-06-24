#!/usr/bin/env python3
"""
Extract HTML, CSS, and JS from home.blade.php with correct relative paths
"""

import re
from pathlib import Path

# Read the original file
input_file = Path(r'd:\Laragon\www\ipaau-cms\resources\views\frontend\home.blade.php')
output_html = Path(r'd:\Laragon\www\ipaau-cms\public\home-new.html')
output_css = Path(r'd:\Laragon\www\ipaau-cms\public\assets\css\home.css')
output_js = Path(r'd:\Laragon\www\ipaau-cms\public\assets\js\home.js')

print("[INFO] Reading original file...")
with open(input_file, 'r', encoding='utf-8') as f:
    content = f.read()

# Extract all CSS from <style> tags
print("[INFO] Extracting CSS...")
css_pattern = r'<style[^>]*>(?P<css>.*?)</style>'
css_matches = re.findall(css_pattern, content, re.DOTALL)

# Combine all CSS
all_css = []
for css in css_matches:
    css = re.sub(r'/\*# sourceMappingURL:.*?\*/', '', css)
    css = re.sub(r'@charset "UTF-8";', '', css)
    css = re.sub(r'@@import[^;]+;', '', css)
    all_css.append(css.strip())

combined_css = '\n\n'.join(all_css)

# Process HTML content - remove all <style> tags
print("[INFO] Processing HTML...")
html_content = content

# Remove all <style> tags
html_content = re.sub(r'<style[^>]*>.*?</style>', '', html_content, flags=re.DOTALL)

# Replace absolute paths with relative paths
print("[INFO] Fixing asset paths...")
html_content = html_content.replace('href="/assets/', 'href="assets/')
html_content = html_content.replace('src="/assets/', 'src="assets/')

# Add link to external CSS in <head>
head_pattern = r'(<head[^>]*>)'
replacement = r'\1\n    <link rel="stylesheet" href="assets/css/home.css">'
html_content = re.sub(head_pattern, replacement, html_content)

# Add script tag for external JS before closing body
body_end_pattern = r'(</body>)'
replacement = r'    <script src="assets/js/home.js"></script>\n\1'
html_content = re.sub(body_end_pattern, replacement, html_content)

# Remove the original entry-client.jsx script
html_content = re.sub(r'<script type="module" src="/assets/entry-client\.jsx"></script>', '', html_content)
html_content = re.sub(r'<script type="module" src="assets/entry-client\.jsx"></script>', '', html_content)

# Write HTML file
print("[INFO] Writing HTML file...")
with open(output_html, 'w', encoding='utf-8') as f:
    f.write(html_content)

print("[OK] HTML file created: {}".format(output_html))

# Write CSS file
print("[INFO] Writing CSS file...")
with open(output_css, 'w', encoding='utf-8') as f:
    f.write(combined_css)

print("[OK] CSS file created: {}".format(output_css))
print("  CSS size: {:,} characters".format(len(combined_css)))

# Create JS file
js_content = """// IPA Home Page - Interactive JavaScript
document.addEventListener('DOMContentLoaded', function() {
    console.log('IPA Home Page loaded');
    initMobileMenu();
    initTabs();
    initCarousels();
    initBackToTop();
    initDropdowns();
    initNewsletterForm();
});

function initMobileMenu() {
    const menuToggle = document.querySelector('.mobile-menu-toggle');
    const mobileNav = document.querySelector('.mobile-nav');
    if (menuToggle && mobileNav) {
        menuToggle.addEventListener('click', function() {
            const isExpanded = this.getAttribute('aria-expanded') === 'true';
            this.classList.toggle('active');
            mobileNav.classList.toggle('active');
            this.setAttribute('aria-expanded', !isExpanded);
        });
    }
}

function initTabs() {
    const tabButtons = document.querySelectorAll('button[data-tab], [role="tab"]');
    tabButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const tabId = this.getAttribute('data-tab') || this.getAttribute('aria-controls');
            const tabList = this.closest('[role="tablist"]') || this.parentElement;
            const allTabs = tabList.querySelectorAll('button[data-tab], [role="tab"]');
            allTabs.forEach(tab => {
                tab.classList.remove('active');
                tab.setAttribute('aria-selected', 'false');
            });
            const allPanels = document.querySelectorAll('[role="tabpanel"], .tab-panel, [data-type="tabPanel"]');
            allPanels.forEach(panel => {
                panel.classList.remove('active');
                panel.setAttribute('aria-hidden', 'true');
            });
            this.classList.add('active');
            this.setAttribute('aria-selected', 'true');
            if (tabId) {
                const targetPanel = document.getElementById(tabId) || 
                                   document.querySelector(`[data-tab-id="${tabId}"]`) ||
                                   document.querySelector(`.tab-panel[data-tab="${tabId}"]`);
                if (targetPanel) {
                    targetPanel.classList.add('active');
                    targetPanel.setAttribute('aria-hidden', 'false');
                }
            }
        });
    });
}

function initCarousels() {
    const carousels = document.querySelectorAll('.swiper, .testimonial-carousel');
    carousels.forEach((carousel, index) => {
        const wrapper = carousel.querySelector('.swiper-wrapper');
        if (!wrapper) return;
        const slides = wrapper.querySelectorAll('.swiper-slide');
        const prevBtn = carousel.querySelector('.swiper-button-prev, .testimonial-card__nav-button_prev');
        const nextBtn = carousel.querySelector('.swiper-button-next, .testimonial-card__nav-button_next');
        const pagination = carousel.querySelector('.swiper-pagination');
        const autoplayBtn = carousel.querySelector('.testimonial-card__navigation-autoplay-button');
        
        let currentSlide = 0;
        let autoplayInterval = null;
        let isAutoplayEnabled = true;
        
        const getSlideWidth = () => slides[0]?.offsetWidth || 0;
        
        const goToSlide = (index) => {
            if (index < 0 || index >= slides.length) return;
            currentSlide = index;
            const offset = -currentSlide * getSlideWidth();
            if (wrapper) {
                wrapper.style.transform = `translateX(${offset}px)`;
                wrapper.style.transition = 'transform 0.3s ease';
            }
            if (pagination) {
                const bullets = pagination.querySelectorAll('.swiper-pagination-bullet');
                bullets.forEach((bullet, i) => {
                    bullet.classList.toggle('swiper-pagination-bullet-active', i === currentSlide);
                });
            }
        };
        
        const nextSlide = () => {
            const nextIndex = (currentSlide + 1) % slides.length;
            goToSlide(nextIndex);
        };
        
        const prevSlide = () => {
            const prevIndex = (currentSlide - 1 + slides.length) % slides.length;
            goToSlide(prevIndex);
        };
        
        const startAutoplay = () => {
            if (autoplayInterval) return;
            autoplayInterval = setInterval(() => {
                if (isAutoplayEnabled) nextSlide();
            }, 5000);
        };
        
        const stopAutoplay = () => {
            if (autoplayInterval) {
                clearInterval(autoplayInterval);
                autoplayInterval = null;
            }
        };
        
        if (nextBtn) nextBtn.addEventListener('click', (e) => { e.preventDefault(); nextSlide(); stopAutoplay(); setTimeout(startAutoplay, 10000); });
        if (prevBtn) prevBtn.addEventListener('click', (e) => { e.preventDefault(); prevSlide(); stopAutoplay(); setTimeout(startAutoplay, 10000); });
        
        if (pagination) {
            const bullets = pagination.querySelectorAll('.swiper-pagination-bullet');
            bullets.forEach((bullet, i) => {
                bullet.addEventListener('click', (e) => { e.preventDefault(); goToSlide(i); stopAutoplay(); setTimeout(startAutoplay, 10000); });
            });
        }
        
        if (autoplayBtn) {
            autoplayBtn.addEventListener('click', (e) => {
                e.preventDefault();
                isAutoplayEnabled = !isAutoplayEnabled;
                const isPressed = autoplayBtn.getAttribute('aria-pressed') === 'true';
                autoplayBtn.setAttribute('aria-pressed', !isPressed);
                if (isAutoplayEnabled) startAutoplay();
                else stopAutoplay();
            });
        }
        
        carousel.addEventListener('mouseenter', stopAutoplay);
        carousel.addEventListener('mouseleave', () => { if (isAutoplayEnabled) startAutoplay(); });
        
        goToSlide(0);
        startAutoplay();
    });
}

function initBackToTop() {
    const backToTop = document.querySelector('button[aria-label*="top"]');
    if (backToTop) {
        backToTop.style.opacity = '0';
        backToTop.style.transition = 'opacity 0.3s ease';
        window.addEventListener('scroll', function() {
            backToTop.style.opacity = window.scrollY > 300 ? '1' : '0';
        });
        backToTop.addEventListener('click', function() {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }
}

function initDropdowns() {
    const dropdowns = document.querySelectorAll('[data-type="menu-wrapper-0"]');
    dropdowns.forEach(dropdown => {
        const trigger = dropdown.querySelector('a, button');
        const panel = dropdown.querySelector('[data-type="megamenu-panel"]');
        if (trigger && panel) {
            dropdown.addEventListener('mouseenter', () => panel.classList.remove('hidden'));
            dropdown.addEventListener('mouseleave', () => panel.classList.add('hidden'));
            trigger.addEventListener('click', function(e) {
                if (window.innerWidth < 1024) {
                    e.preventDefault();
                    panel.classList.toggle('hidden');
                }
            });
        }
    });
}

function initNewsletterForm() {
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const emailInput = form.querySelector('input[type="email"]');
            if (!emailInput) return;
            e.preventDefault();
            if (!emailInput.value || !emailInput.value.includes('@')) {
                emailInput.focus();
                return;
            }
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = 'Subscribed!';
            submitBtn.disabled = true;
            setTimeout(() => {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
                form.reset();
            }, 2000);
        });
    });
}
"""

print("[INFO] Writing JS file...")
with open(output_js, 'w', encoding='utf-8') as f:
    f.write(js_content)

print("[OK] JS file created: {}".format(output_js))
print("\n[OK] Extraction complete!")
