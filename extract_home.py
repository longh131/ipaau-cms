#!/usr/bin/env python3
"""
Extract HTML, CSS, and JS from home.blade.php to create a pure HTML+CSS+JS version
"""

import re
from pathlib import Path

# Read the original file
input_file = Path(r'd:\Laragon\www\ipaau-cms\resources\views\frontend\home.blade.php')
output_html = Path(r'd:\Laragon\www\ipaau-cms\public\home-new.html')
output_css = Path(r'd:\Laragon\www\ipaau-cms\public\assets\css\home.css')
output_js = Path(r'd:\Laragon\www\ipaau-cms\public\assets\js\home.js')

with open(input_file, 'r', encoding='utf-8') as f:
    content = f.read()

# Extract all CSS from <style> tags
css_pattern = r'<style[^>]*>(?P<css>.*?)</style>'
css_matches = re.findall(css_pattern, content, re.DOTALL)

# Combine all CSS
all_css = []
for css in css_matches:
    # Remove sourceMappingURL comments
    css = re.sub(r'/\*# sourceMappingURL:.*?\*/', '', css)
    # Remove @charset declarations
    css = re.sub(r'@charset "UTF-8";', '', css)
    # Remove multiple @@import
    css = re.sub(r'@@import[^;]+;', '', css)
    all_css.append(css.strip())

combined_css = '\n\n'.join(all_css)

# Create HTML file - remove all <style> tags and replace with link to external CSS
html_content = content

# Remove all <style> tags
html_content = re.sub(r'<style[^>]*>.*?</style>', '', html_content, flags=re.DOTALL)

# Add link to external CSS in <head>
head_pattern = r'(<head[^>]*>)'
replacement = r'\1\n    <link rel="stylesheet" href="/assets/css/home.css">'
html_content = re.sub(head_pattern, replacement, html_content)

# Add script tag for external JS before closing body
body_end_pattern = r'(</body>)'
replacement = r'    <script src="/assets/js/home.js"></script>\n\1'
html_content = re.sub(body_end_pattern, replacement, html_content)

# Remove the original entry-client.jsx script
html_content = re.sub(r'<script type="module" src="/assets/entry-client\.jsx"></script>', '', html_content)

# Write HTML file
with open(output_html, 'w', encoding='utf-8') as f:
    f.write(html_content)

print(f"[OK] HTML file created: {output_html}")

# Write CSS file
with open(output_css, 'w', encoding='utf-8') as f:
    f.write(combined_css)

print(f"[OK] CSS file created: {output_css}")
print(f"  CSS size: {len(combined_css):,} characters")

# Create a basic JS file for interactions
js_content = """// IPA Home Page - Interactive JavaScript
document.addEventListener('DOMContentLoaded', function() {
    console.log('IPA Home Page loaded');
    
    // Initialize mobile menu
    initMobileMenu();
    
    // Initialize tabs
    initTabs();
    
    // Initialize carousel
    initCarousel();
    
    // Initialize back to top button
    initBackToTop();
});

// Mobile Menu Toggle
function initMobileMenu() {
    const menuToggle = document.querySelector('.mobile-menu-toggle');
    const mobileNav = document.querySelector('.mobile-nav');
    
    if (menuToggle && mobileNav) {
        menuToggle.addEventListener('click', function() {
            this.classList.toggle('active');
            mobileNav.classList.toggle('active');
        });
    }
}

// Tab Switching
function initTabs() {
    const tabButtons = document.querySelectorAll('[data-tab]');
    const tabPanels = document.querySelectorAll('[data-type="tabPanel"]');
    
    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            const tabId = this.getAttribute('data-tab');
            
            // Remove active from all
            tabButtons.forEach(btn => btn.classList.remove('active'));
            tabPanels.forEach(panel => panel.classList.remove('active'));
            
            // Add active to current
            this.classList.add('active');
            const targetPanel = document.getElementById(tabId);
            if (targetPanel) {
                targetPanel.classList.add('active');
            }
        });
    });
}

// Carousel
function initCarousel() {
    const carousels = document.querySelectorAll('.swiper');
    
    carousels.forEach(carousel => {
        // Initialize carousel logic here
        console.log('Carousel initialized', carousel);
    });
}

// Back to Top
function initBackToTop() {
    const backToTop = document.querySelector('[data-idx="0"]');
    
    if (backToTop) {
        window.addEventListener('scroll', function() {
            if (window.scrollY > 300) {
                backToTop.style.opacity = '1';
            } else {
                backToTop.style.opacity = '0';
            }
        });
        
        backToTop.addEventListener('click', function() {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }
}
"""

with open(output_js, 'w', encoding='utf-8') as f:
    f.write(js_content)

print(f"[OK] JS file created: {output_js}")

print("\n[OK] Extraction complete!")
print(f"   HTML: {output_html}")
print(f"   CSS: {output_css}")
print(f"   JS: {output_js}")
