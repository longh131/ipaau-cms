// IPA Home Page - Interactive JavaScript
document.addEventListener('DOMContentLoaded', function() {
    console.log('IPA Home Page loaded');
    initMobileMenu();
    initTabs();
    initCarousels();
    initBackToTop();
    initDropdowns();
    initNewsletterForm();
    initBlobAnimationOptimization();
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
    const navItems = document.querySelectorAll('[data-type="desktop-navigation"] [data-level="0"]');
    
    // 点击页面其他地方关闭所有下拉菜单
    document.addEventListener('click', function(e) {
        const isDropdownClick = e.target.closest('[data-type="desktop-navigation"] [data-level="0"]');
        const isPanelClick = e.target.closest('[data-type="megamenu-panel"]');
        
        if (!isDropdownClick && !isPanelClick) {
            navItems.forEach(item => {
                item.classList.remove('active');
                item.classList.add('inactive');
            });
        }
    });
    
    navItems.forEach(item => {
        const dropdown = item.querySelector('[data-type="menu-wrapper-0"]');
        
        if (dropdown) {
            // 点击展开/收起（桌面和移动端一致）
            const trigger = dropdown.querySelector('a');
            if (trigger) {
                trigger.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    // 先关闭其他所有下拉菜单
                    navItems.forEach(otherItem => {
                        if (otherItem !== item) {
                            otherItem.classList.remove('active');
                            otherItem.classList.add('inactive');
                        }
                    });
                    
                    // 切换当前下拉菜单
                    const isActive = item.classList.contains('active');
                    if (isActive) {
                        item.classList.remove('active');
                        item.classList.add('inactive');
                    } else {
                        item.classList.add('active');
                        item.classList.remove('inactive');
                    }
                });
            }
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

function initBlobAnimationOptimization() {
    // 获取所有blob背景容器
    const blobContainers = document.querySelectorAll('.blobBackground .blobContainer');
    
    if (blobContainers.length === 0) {
        console.log('Blob containers not found');
        return;
    }
    
    console.log('Found', blobContainers.length, 'blob containers');
    
    // 为每个容器中的色块配置独立的动画参数
    const animations = [];
    
    blobContainers.forEach((container) => {
        const blobs = container.querySelectorAll('div');
        blobs.forEach((blob, index) => {
            // 根据元素的位置/类名分配不同的动画类型和速度
            let animType = 'horizontal';
            let speed = 0.1;
            let range = 100;
            
            // 根据元素特征设置不同动画
            if (blob.classList.contains('purple')) {
                animType = 'horizontal';
                speed = 0.12;
                range = 120;
            } else if (blob.classList.contains('blue')) {
                animType = 'vertical';
                speed = 0.1;
                range = 100;
            } else if (blob.classList.contains('orange')) {
                animType = 'rotate';
                speed = 0.08;
                range = 360;
            }
            
            animations.push({
                element: blob,
                type: animType,
                speed: speed,
                range: range,
                phase: index * 60,  // 错开相位，更自然
                rotX: 0,
                rotY: 0
            });
        });
    });
    
    let startTime = null;
    let animFrame = null;
    
    function animate(timestamp) {
        if (!startTime) startTime = timestamp;
        const elapsed = timestamp - startTime;
        const seconds = elapsed / 1000;
        
        animations.forEach(anim => {
            const angle = seconds * Math.PI * 2 * anim.speed + (anim.phase * Math.PI / 180);
            
            if (anim.type === 'horizontal') {
                const offsetX = Math.sin(angle) * anim.range;
                anim.element.style.transform = `translate3d(${offsetX}px, 0, 0)`;
            }
            else if (anim.type === 'vertical') {
                const offsetY = Math.sin(angle) * anim.range;
                anim.element.style.transform = `translate3d(0, ${offsetY}px, 0)`;
            }
            else if (anim.type === 'rotate') {
                const rotation = (seconds * anim.speed * 360) % 360;
                const offsetX = Math.sin(angle * 0.5) * (anim.range * 0.3);
                const offsetY = Math.cos(angle * 0.7) * (anim.range * 0.3);
                anim.element.style.transform = `translate3d(${offsetX}px, ${offsetY}px, 0) rotate(${rotation}deg)`;
            }
        });
        
        animFrame = requestAnimationFrame(animate);
    }
    
    // 启动动画
    animFrame = requestAnimationFrame(animate);
    console.log('Blob animation started with requestAnimationFrame');
    
    // 页面隐藏时暂停动画（节省资源）
    document.addEventListener('visibilitychange', () => {
        if (document.hidden) {
            if (animFrame) {
                cancelAnimationFrame(animFrame);
                animFrame = null;
                console.log('Blob animation paused (page hidden)');
            }
        } else {
            if (!animFrame) {
                startTime = null;
                animFrame = requestAnimationFrame(animate);
                console.log('Blob animation resumed (page visible)');
            }
        }
    });
}
