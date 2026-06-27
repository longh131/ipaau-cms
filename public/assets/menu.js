/**
 * 菜单交互脚本 - 原生JavaScript实现
 * 实现与React版本相同的菜单点击下拉效果
 */

(function() {
    'use strict';

    // 状态管理
    let activeMenuIndex = null;
    let activeSubMenuIndex = -1;
    let mobileMenuOpen = false;

    // ==================== 桌面端菜单 ====================

    // 获取所有菜单项
    function getMenuItems() {
        return document.querySelectorAll('[data-type="desktop-navigation"] [data-level="0"]');
    }

    // 获取菜单项的面板
    function getMenuPanel(menuItem) {
        return menuItem.querySelector('[data-type="megamenu-panel"]');
    }

    // 获取所有子菜单项
    function getSubMenuItems(menuItem) {
        return menuItem.querySelectorAll('[data-type="megamenu-level-1"] > li');
    }

    // 获取子菜单面板
    function getSubMenuPanel(subMenuItem) {
        return subMenuItem.querySelector('[data-type="megamenu-level-2"]');
    }

    // 切换菜单状态
    function toggleMenu(menuItem, index) {
        const panel = getMenuPanel(menuItem);
        if (!panel) return;

        // 如果点击的是当前激活的菜单，则关闭它
        if (activeMenuIndex === index) {
            closeAllMenus();
            return;
        }

        // 打开菜单前先关闭搜索和其他面板
        closeSearch();
        closeAllMenus();

        // 激活当前菜单
        activeMenuIndex = index;
        menuItem.classList.remove('inactive');
        menuItem.classList.add('active');
        panel.classList.remove('hidden');
        panel.classList.add('block');

        // 自动打开第一个子菜单
        const subMenuItems = getSubMenuItems(menuItem);
        if (subMenuItems.length > 0) {
            openSubMenu(subMenuItems[0], 0);
        }
    }

    // 打开子菜单
    function openSubMenu(subMenuItem, index) {
        const panel = getSubMenuPanel(subMenuItem);
        if (!panel) return;

        // 关闭同级其他子菜单
        const parentUl = subMenuItem.parentElement;
        const siblings = parentUl.querySelectorAll('li');
        siblings.forEach((sibling, idx) => {
            if (idx !== index) {
                closeSubMenu(sibling);
            }
        });

        // 激活当前子菜单
        activeSubMenuIndex = index;
        subMenuItem.classList.remove('inactive');
        subMenuItem.classList.add('active');

        if (panel) {
            panel.classList.remove('hidden');
            panel.classList.add('block');
        }
    }

    // 关闭子菜单
    function closeSubMenu(subMenuItem) {
        const panel = getSubMenuPanel(subMenuItem);
        subMenuItem.classList.remove('active');
        subMenuItem.classList.add('inactive');

        if (panel) {
            panel.classList.remove('block');
            panel.classList.add('hidden');
        }
    }

    // 关闭所有菜单
    function closeAllMenus() {
        const menuItems = getMenuItems();
        menuItems.forEach(menuItem => {
            const panel = getMenuPanel(menuItem);
            menuItem.classList.remove('active');
            menuItem.classList.add('inactive');

            if (panel) {
                panel.classList.remove('block');
                panel.classList.add('hidden');
            }

            // 关闭所有子菜单
            const subMenuItems = getSubMenuItems(menuItem);
            subMenuItems.forEach(subMenuItem => {
                closeSubMenu(subMenuItem);
            });
        });

        activeMenuIndex = null;
        activeSubMenuIndex = -1;
    }

    // 初始化桌面端菜单事件
    function initMenuEvents() {
        const menuItems = getMenuItems();

        menuItems.forEach((menuItem, index) => {
            const menuWrapper = menuItem.querySelector('[data-type="menu-wrapper-0"]');
            if (!menuWrapper) return;

            const link = menuWrapper.querySelector('a');

            if (link) {
                link.addEventListener('click', function(e) {
                    // 只在桌面端处理点击
                    if (window.innerWidth >= 1280) {
                        e.preventDefault();
                        e.stopPropagation();
                        toggleMenu(menuItem, index);
                    }
                });
            }

            // 子菜单点击事件
            const subMenuItems = getSubMenuItems(menuItem);
            subMenuItems.forEach((subMenuItem, subIndex) => {
                const subMenuWrapper = subMenuItem.querySelector('[data-type="menu-wrapper-1"]');
                if (!subMenuWrapper) return;

                const subLink = subMenuWrapper.querySelector('a');

                if (subLink && subLink.getAttribute('data-children') === 'true') {
                    subLink.addEventListener('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        openSubMenu(subMenuItem, subIndex);
                    });
                }
            });
        });
    }

    // ==================== 移动端菜单 ====================

    // 获取移动端菜单触发器容器
    function getMobileMenuTriggerContainer() {
        return document.querySelector('[data-type="mobileNavTrigger"]');
    }

    // 获取移动端菜单触发器按钮
    function getMobileMenuTrigger() {
        const container = getMobileMenuTriggerContainer();
        if (container) {
            return container.querySelector('button');
        }
        return null;
    }

    // 获取移动端导航容器
    function getMobileNavigation() {
        return document.querySelector('[data-type="mobile-navigation-wrapper"]');
    }

    // 获取移动端导航内容区域
    function getMobileNavigationContent() {
        return document.querySelector('[data-type="mobile-navigation-content"]');
    }

    // 获取移动端触发器内部的menu div
    function getMobileMenuIcon() {
        const container = getMobileMenuTriggerContainer();
        if (container) {
            // 斜杠在CSS选择器中需要转义，使用双反斜杠
            return container.querySelector('[class*="group\\/menu"]');
        }
        return null;
    }

    // 切换移动端菜单
    function toggleMobileMenu() {
        const triggerContainer = getMobileMenuTriggerContainer();
        const trigger = getMobileMenuTrigger();
        const mobileNav = getMobileNavigation();
        const mobileNavContent = getMobileNavigationContent();
        const menuIcon = getMobileMenuIcon();
        const willOpen = !mobileMenuOpen;

        if (willOpen) {
            closeSearch();
            closeAllMenus();
        }
        
        if (trigger && mobileNav && triggerContainer) {
            mobileMenuOpen = !mobileMenuOpen;
            
            // 切换触发器容器状态（用于CSS peer选择器）
            triggerContainer.classList.toggle('active');
            triggerContainer.classList.toggle('inactive');
            
            // 切换触发器按钮状态
            trigger.classList.toggle('active');
            trigger.classList.toggle('inactive');
            
            // 切换导航状态
            mobileNav.classList.toggle('active');
            mobileNav.classList.toggle('inactive');
            
            // 切换导航内容的高度
            if (mobileNavContent) {
                mobileNavContent.classList.toggle('active');
                mobileNavContent.classList.toggle('inactive');
            }
            
            // 切换图标状态 - 这是关键：切换group/menu的active/inactive类
            if (menuIcon) {
                menuIcon.classList.toggle('active');
                menuIcon.classList.toggle('inactive');
            }
            
            // 调整高度（使用CSS transition而不是JS设置）
            if (mobileMenuOpen) {
                mobileNav.style.maxHeight = '2000px'; // 足够大的值
            } else {
                mobileNav.style.maxHeight = '0px';
            }
        }
    }

    // 初始化移动端菜单
    function initMobileMenu() {
        const trigger = getMobileMenuTrigger();
        
        if (trigger) {
            trigger.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                toggleMobileMenu();
            });
        }

        // 移动端菜单项点击事件（展开二级菜单）
        const mobileMenuItems = document.querySelectorAll('[data-type="mobile-navigation"] [data-level="0"]');
        mobileMenuItems.forEach((menuItem) => {
            const button = menuItem.querySelector('[data-type="menu-wrapper-0"] button');
            if (button) {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();

                    const panel = menuItem.querySelector('[data-type="megamenu-panel"]');
                    const willOpen = menuItem.classList.contains('inactive');

                    menuItem.classList.toggle('inactive');
                    menuItem.classList.toggle('active');

                    if (panel) {
                        panel.classList.toggle('hidden', !willOpen);
                        panel.classList.toggle('block', willOpen);

                        if (!willOpen) {
                            resetMobileLevel2Menus(panel);
                        }
                    }
                });
            }
        });

        initMobileLevel2Menus();
    }

    function resetMobileLevel2Menus(container) {
        if (!container) return;

        container.querySelectorAll('[data-type="megamenu-level-1"] > li').forEach((menuItem) => {
            menuItem.classList.remove('active');
            menuItem.classList.add('inactive');
        });

        container.querySelectorAll('[data-type="megamenu-level-2"]').forEach((panel) => {
            panel.classList.remove('active', 'max-xl:block', 'block');
            panel.classList.add('inactive', 'max-xl:hidden');
            panel.querySelectorAll('a').forEach((link) => {
                link.setAttribute('tabindex', '-1');
                link.setAttribute('aria-hidden', 'true');
            });
        });
    }

    function setMobileLevel2Open(menuItem, level2Panel, isOpen) {
        menuItem.classList.toggle('active', isOpen);
        menuItem.classList.toggle('inactive', !isOpen);
        level2Panel.classList.toggle('active', isOpen);
        level2Panel.classList.toggle('max-xl:block', isOpen);
        level2Panel.classList.toggle('max-xl:hidden', !isOpen);
        level2Panel.classList.toggle('hidden', !isOpen);
        level2Panel.classList.toggle('block', isOpen);

        level2Panel.querySelectorAll('a').forEach((link) => {
            link.setAttribute('tabindex', isOpen ? '0' : '-1');
            link.setAttribute('aria-hidden', isOpen ? 'false' : 'true');
        });
    }

    function initMobileLevel2Menus() {
        const mobileNav = document.querySelector('[data-type="mobile-navigation"]');
        if (!mobileNav) return;

        mobileNav.querySelectorAll('[data-type="megamenu-level-1"] > li').forEach((menuItem) => {
            const wrapper = menuItem.querySelector('[data-type="menu-wrapper-1"]');
            const level2Panel = menuItem.querySelector('[data-type="megamenu-level-2"]');
            if (!wrapper || !level2Panel) return;

            const button = wrapper.querySelector('button');
            const link = wrapper.querySelector('a[data-children="true"]');
            if (!button) return;

            function toggleLevel2() {
                const isOpen = level2Panel.classList.contains('max-xl:block');

                if (isOpen) {
                    setMobileLevel2Open(menuItem, level2Panel, false);
                    return;
                }

                menuItem.parentElement.querySelectorAll(':scope > li').forEach((sibling) => {
                    if (sibling === menuItem) return;
                    const siblingPanel = sibling.querySelector('[data-type="megamenu-level-2"]');
                    if (siblingPanel) {
                        setMobileLevel2Open(sibling, siblingPanel, false);
                    }
                });

                setMobileLevel2Open(menuItem, level2Panel, true);
            }

            button.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                toggleLevel2();
            });

            if (link) {
                link.addEventListener('click', function(e) {
                    if (window.innerWidth >= 1280) return;
                    e.preventDefault();
                    e.stopPropagation();
                    toggleLevel2();
                });
            }
        });
    }

    // ==================== 搜索功能 ====================

    // 获取搜索触发器容器
    function getSearchTriggerContainer() {
        return document.querySelector('[data-type="search"]');
    }

    // 获取搜索触发器按钮
    function getSearchTrigger() {
        const container = getSearchTriggerContainer();
        if (container) {
            return container.querySelector('button');
        }
        return null;
    }

    // 获取搜索表单
    function getSearchForm() {
        return document.querySelector('[data-type="searchForm"]');
    }

    function isSearchOpen() {
        const triggerContainer = getSearchTriggerContainer();
        return !!(triggerContainer && triggerContainer.classList.contains('active'));
    }

    function closeSearch() {
        const triggerContainer = getSearchTriggerContainer();
        const searchForm = getSearchForm();

        if (!triggerContainer || !searchForm || !isSearchOpen()) {
            return;
        }

        triggerContainer.classList.remove('active');
        triggerContainer.classList.add('inactive');
        searchForm.classList.remove('active');
        searchForm.classList.add('inactive');

        const form = searchForm.querySelector('form');
        if (form) {
            form.classList.remove('active');
            form.classList.add('inactive');
        }
    }

    // 获取搜索图标容器（用于切换图标）
    function getSearchIcon() {
        const container = getSearchTriggerContainer();
        if (container) {
            return container.querySelector('svg');
        }
        return null;
    }

    // 切换搜索框显示
    function toggleSearch() {
        const triggerContainer = getSearchTriggerContainer();
        const searchForm = getSearchForm();
        
        if (!triggerContainer || !searchForm) {
            return;
        }

        const willOpen = !isSearchOpen();

        if (willOpen) {
            closeAllMenus();
            if (mobileMenuOpen) {
                toggleMobileMenu();
            }
        }
        
        // 切换触发器容器状态
        triggerContainer.classList.toggle('active');
        triggerContainer.classList.toggle('inactive');
        
        // 切换搜索表单状态
        searchForm.classList.toggle('active');
        searchForm.classList.toggle('inactive');
        
        // 切换搜索表单内部的form状态（用于动画）
        const form = searchForm.querySelector('form');
        if (form) {
            form.classList.toggle('active');
            form.classList.toggle('inactive');
        }
    }

    // 初始化搜索功能
    function initSearch() {
        const trigger = document.querySelector('[data-type="search"] button');
        
        if (!trigger) {
            return;
        }
        
        trigger.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            toggleSearch();
        });
    }

    // ==================== 选项卡效果 ====================

    const TABBED_CONTENT_PRESETS = [
        {
            tagline: '声誉与认可',
            title: '专业声誉，全球认可',
            description: 'IPA 致力于提升会计行业的专业声誉与国际认可度，为会员提供权威资质背书与职业发展支持。',
            cta: '了解更多',
            image: 'assets/img/20231201-ipa-national-congress-day-3-0471.jpg'
        },
        {
            tagline: '倡导（AU）',
            title: '为中小企业发声，推动行业变革',
            description: '我们在澳大利亚积极倡导中小企业与会计专业人士的权益，参与政策讨论，为会员争取更有利的营商环境。',
            cta: '查看倡导工作',
            image: 'assets/img/ipaportdouglas_2024_congress_day1_0156.jpg'
        },
        {
            tagline: '政策（AU）',
            title: '深度参与政策制定与行业规范',
            description: 'IPA 密切关注澳大利亚财税政策动态，为会员解读法规变化，提供合规指引与专业建议。',
            cta: '浏览政策解读',
            image: 'assets/img/ipaportdouglas_2024_congress_day1_0302.jpg'
        },
        {
            tagline: 'PA新闻',
            title: '掌握行业最新动态与资讯',
            description: '及时了解 Public Accountants 领域的新闻、活动与行业趋势，助您把握先机，持续成长。',
            cta: '阅读更多新闻',
            image: 'assets/img/20231201-ipa-national-congress-day-3-0471.jpg'
        }
    ];

    function applyTabPanelContent(panel, preset) {
        const eyebrow = panel.querySelector('.eyebrow-xl');
        const titleEl = panel.querySelector('[data-type="section-title"] h3, h3');
        const descEl = panel.querySelector('[data-type="section-description"] .text-primary');
        const ctaEl = panel.querySelector('.cta-content');

        if (eyebrow) eyebrow.textContent = preset.tagline;
        if (titleEl) titleEl.textContent = preset.title;
        if (descEl) descEl.textContent = preset.description;
        if (ctaEl) ctaEl.textContent = preset.cta;
    }

    function setTabImage(imgWrapper, imagePath) {
        if (!imgWrapper || !imagePath) return;

        const img = imgWrapper.querySelector('img');
        if (img) {
            img.src = imagePath;
        }
    }

    function initTabbedContentSections() {
        document.querySelectorAll('section[data-type="tabbedContent"]').forEach(section => {
            const tabGroups = Array.from(section.querySelectorAll('.flex.flex-wrap.gap-4')).filter(
                group => group.querySelector('button.tab, .tab')
            );
            if (tabGroups.length === 0) return;

            const tabCount = tabGroups[0].querySelectorAll('button.tab, .tab').length;
            const contentColumn = section.querySelector('.flex.h-full.flex-col.items-start');
            if (!contentColumn) return;

            const imgWrapper = section.querySelector('.img-shape-acorn.img-wrapper');
            let panels = Array.from(contentColumn.querySelectorAll('[data-type="tab-content"]'));

            if (panels.length === 0) {
                const original = contentColumn.querySelector('.space-y-8');
                if (!original) return;

                original.setAttribute('data-type', 'tab-content');
                panels = [original];

                for (let i = 1; i < tabCount; i++) {
                    const clone = original.cloneNode(true);
                    clone.setAttribute('data-type', 'tab-content');
                    clone.classList.add('hidden');
                    if (TABBED_CONTENT_PRESETS[i]) {
                        applyTabPanelContent(clone, TABBED_CONTENT_PRESETS[i]);
                    }
                    contentColumn.appendChild(clone);
                    panels.push(clone);
                }
            }

            function switchTab(index) {
                tabGroups.forEach(group => {
                    group.querySelectorAll('button.tab, .tab').forEach((tab, i) => {
                        tab.classList.toggle('active', i === index);
                    });
                });

                panels.forEach((panel, i) => {
                    panel.classList.toggle('hidden', i !== index);
                });

                const panel = panels[index];
                const imageUrl = panel?.dataset?.tabImage;
                if (imgWrapper && imageUrl) {
                    setTabImage(imgWrapper, imageUrl);
                    return;
                }

                const preset = TABBED_CONTENT_PRESETS[index];
                if (imgWrapper && preset) {
                    setTabImage(imgWrapper, preset.image);
                }
            }

            tabGroups.forEach(group => {
                group.querySelectorAll('button.tab, .tab').forEach((tab, index) => {
                    tab.addEventListener('click', function(e) {
                        e.preventDefault();
                        switchTab(index);
                    });
                });
            });

            switchTab(0);
        });
    }

    // 初始化选项卡效果
    function initTabs() {
        // 通用选项卡处理
        const tabContainers = document.querySelectorAll('[data-type="tabs-container"]');
        
        tabContainers.forEach(container => {
            const tabs = container.querySelectorAll('[data-type="tab"]');
            const panels = container.querySelectorAll('[data-type="tab-panel"]');
            
            tabs.forEach((tab, index) => {
                tab.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    tabs.forEach(t => t.classList.remove('active'));
                    panels.forEach(p => p.classList.remove('active'));
                    
                    tab.classList.add('active');
                    if (panels[index]) {
                        panels[index].classList.add('active');
                    }
                });
            });
        });

        initTabbedContentSections();

        // 特殊处理EVENTS COURSES ONLINE CPD选项卡
        const eventTabContainers = document.querySelectorAll('[data-type="tab-container"]');
        
        eventTabContainers.forEach(container => {
            const tabs = container.querySelectorAll('button[data-type="tab"], .tab button, .tab');
            const panels = container.querySelectorAll('[data-type="tab-panel"], [data-type="course-content"]');
            
            tabs.forEach((tab, index) => {
                tab.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    tabs.forEach(t => t.classList.remove('active'));
                    panels.forEach(p => p.classList.remove('active'));
                    
                    tab.classList.add('active');
                    if (panels[index]) {
                        panels[index].classList.add('active');
                    }
                });
            });
        });
    }

    // ==================== 滑动效果（轮播）====================

    // 初始化滑动效果
    function initCarousel() {
        // 处理.testimonial-carousel类的轮播
        console.log('[DEBUG] 开始处理轮播');
        const testimonialCarousels = document.querySelectorAll('.testimonial-carousel');
        console.log('[DEBUG] 找到轮播容器数量:', testimonialCarousels.length);
        
        testimonialCarousels.forEach((carousel) => {
            const swiperWrapper = carousel.querySelector('.swiper-wrapper');
            let slides = carousel.querySelectorAll('.swiper-slide');
            const indicators = carousel.querySelectorAll('.swiper-pagination-bullet');
            const autoplayButton = carousel.querySelector('.testimonial-card__navigation-autoplay-button');
            const autoplayLiveRegion = carousel.querySelector('[aria-live="polite"]');

            if (!swiperWrapper || slides.length === 0) return;

            if (indicators.length > slides.length) {
                const source = slides[slides.length - 1];
                for (let i = slides.length; i < indicators.length; i++) {
                    const clone = source.cloneNode(true);
                    clone.className = 'swiper-slide';
                    clone.setAttribute('aria-label', `${i + 1} / ${indicators.length}`);
                    const quote = clone.querySelector('.testimonial-card__quote');
                    if (quote) {
                        quote.textContent =
                            '"作为 IPA 会员，我深刻感受到协会对专业发展的持续支持，线上培训与行业活动让成长不断延续。"';
                    }
                    const nameEl = clone.querySelector('[itemprop="name"]');
                    if (nameEl) nameEl.textContent = 'Michael Chen';
                    const titleEl = clone.querySelector('.testimonial-card__title');
                    if (titleEl) titleEl.textContent = 'FIPA, Member for 15 Years';
                    swiperWrapper.appendChild(clone);
                }
                slides = carousel.querySelectorAll('.swiper-slide');
            }

            let currentIndex = 0;
            let autoplayInterval = null;
            let isAutoplayEnabled = true;

            function normalizeSlides() {
                slides.forEach((slide) => {
                    slide.style.removeProperty('width');
                    slide.style.removeProperty('margin-right');
                    slide.style.flex = '0 0 100%';
                    slide.style.width = '100%';
                    slide.style.maxWidth = '100%';
                    slide.style.boxSizing = 'border-box';

                    const wrapper = slide.querySelector('.testimonial-card__wrapper');
                    if (wrapper) {
                        wrapper.classList.remove(
                            'swiper-slide-active',
                            'swiper-slide-prev',
                            'swiper-slide-next',
                            'container'
                        );
                    }
                });

                swiperWrapper.style.removeProperty('transition-duration');
            }

            function updateAutoplayUI() {
                if (!autoplayButton) return;

                const playing = isAutoplayEnabled;
                autoplayButton.setAttribute('aria-pressed', playing ? 'true' : 'false');
                autoplayButton.setAttribute('aria-label', playing ? 'Pause autoplay' : 'Resume autoplay');
                autoplayButton.title = playing ? 'Pause autoplay' : 'Resume autoplay';

                const visibleSpan = autoplayButton.querySelector('span[aria-hidden="true"]');
                const srSpan = autoplayButton.querySelector('span.sr-only');
                if (visibleSpan) {
                    visibleSpan.textContent = playing ? 'Pause Autoplay' : 'Resume Autoplay';
                }
                if (srSpan) {
                    srSpan.textContent = playing ? 'Pause autoplay' : 'Resume autoplay';
                }
                if (autoplayLiveRegion) {
                    autoplayLiveRegion.textContent = playing ? 'Autoplay is running' : 'Autoplay is paused';
                }
            }

            function pauseAutoplayTimer() {
                if (autoplayInterval) {
                    clearInterval(autoplayInterval);
                    autoplayInterval = null;
                }
            }

            function startAutoplayTimer() {
                pauseAutoplayTimer();
                if (!isAutoplayEnabled) return;
                autoplayInterval = setInterval(nextSlide, 5000);
            }

            function showSlide(index) {
                if (index < 0) index = slides.length - 1;
                if (index >= slides.length) index = 0;

                currentIndex = index;

                swiperWrapper.style.transform = `translate3d(-${currentIndex * 100}%, 0, 0)`;
                swiperWrapper.style.transition = 'transform 0.5s ease';

                slides.forEach((slide, i) => {
                    slide.classList.remove(
                        'active',
                        'swiper-slide-active',
                        'swiper-slide-prev',
                        'swiper-slide-next',
                        'hidden'
                    );

                    if (i === currentIndex) {
                        slide.classList.add('active', 'swiper-slide-active');
                    } else if (i === currentIndex - 1 || (currentIndex === 0 && i === slides.length - 1)) {
                        slide.classList.add('swiper-slide-prev');
                    } else if (i === currentIndex + 1 || (currentIndex === slides.length - 1 && i === 0)) {
                        slide.classList.add('swiper-slide-next');
                    }
                });

                indicators.forEach((indicator, i) => {
                    indicator.classList.remove('active', 'swiper-pagination-bullet-active');
                    indicator.removeAttribute('aria-current');
                    if (i === currentIndex) {
                        indicator.classList.add('active', 'swiper-pagination-bullet-active');
                        indicator.setAttribute('aria-current', 'true');
                    }
                });
            }

            function nextSlide() {
                showSlide(currentIndex + 1);
            }

            function prevSlide() {
                showSlide(currentIndex - 1);
            }

            function toggleAutoplay() {
                isAutoplayEnabled = !isAutoplayEnabled;
                updateAutoplayUI();
                if (isAutoplayEnabled) {
                    startAutoplayTimer();
                } else {
                    pauseAutoplayTimer();
                }
            }

            carousel.querySelectorAll('.testimonial-card__navigation--prev button').forEach((button) => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    prevSlide();
                });
            });

            carousel.querySelectorAll('.testimonial-card__navigation--next button').forEach((button) => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    nextSlide();
                });
            });

            indicators.forEach((indicator, index) => {
                indicator.addEventListener('click', function(e) {
                    e.preventDefault();
                    showSlide(index);
                });
            });

            if (autoplayButton) {
                autoplayButton.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    toggleAutoplay();
                });
            }

            carousel.addEventListener('mouseenter', pauseAutoplayTimer);
            carousel.addEventListener('mouseleave', function() {
                if (isAutoplayEnabled) {
                    startAutoplayTimer();
                }
            });

            window.addEventListener('resize', function() {
                normalizeSlides();
                showSlide(currentIndex);
            });

            swiperWrapper.style.display = 'flex';
            swiperWrapper.style.flexDirection = 'row';
            normalizeSlides();
            showSlide(0);
            updateAutoplayUI();
            startAutoplayTimer();
        });

        // 处理testimonialCarousel
        const testimonialSections = document.querySelectorAll('section[data-type="testimonialCarousel"]');
        
        testimonialSections.forEach(section => {
            const slides = section.querySelectorAll('[data-type="slide"], .testimonial-slide');
            const prevButton = section.querySelector('[data-type="prev-button"], .prev-button');
            const nextButton = section.querySelector('[data-type="next-button"], .next-button');
            const indicators = section.querySelectorAll('[data-type="indicator"], .carousel-indicator');
            let currentIndex = 0;

            function showSlide(index) {
                if (index < 0) index = slides.length - 1;
                if (index >= slides.length) index = 0;
                
                currentIndex = index;
                
                slides.forEach((slide, i) => {
                    slide.classList.remove('active');
                    slide.classList.add('hidden');
                    if (i === currentIndex) {
                        slide.classList.remove('hidden');
                        slide.classList.add('active');
                    }
                });
                
                indicators.forEach((indicator, i) => {
                    indicator.classList.toggle('active', i === currentIndex);
                });
            }

            function nextSlide() {
                showSlide(currentIndex + 1);
            }

            function prevSlide() {
                showSlide(currentIndex - 1);
            }

            if (nextButton) {
                nextButton.addEventListener('click', nextSlide);
            }
            if (prevButton) {
                prevButton.addEventListener('click', prevSlide);
            }

            indicators.forEach((indicator, index) => {
                indicator.addEventListener('click', () => showSlide(index));
            });

            const interval = setInterval(nextSlide, 5000);
            
            section.addEventListener('mouseenter', () => clearInterval(interval));
            section.addEventListener('mouseleave', () => setInterval(nextSlide, 5000));

            if (slides.length > 0) {
                showSlide(0);
            }
        });

        // 处理通用slider
        const sliders = document.querySelectorAll('[data-type="slider"]');
        
        sliders.forEach(slider => {
            const slides = slider.querySelectorAll('[data-type="slide"]');
            const prevButton = slider.querySelector('[data-type="prev-button"]');
            const nextButton = slider.querySelector('[data-type="next-button"]');
            const indicators = slider.querySelectorAll('[data-type="indicator"]');
            let currentIndex = 0;

            function showSlide(index) {
                if (index < 0) index = slides.length - 1;
                if (index >= slides.length) index = 0;
                
                currentIndex = index;
                
                slides.forEach((slide, i) => {
                    slide.classList.toggle('active', i === currentIndex);
                    slide.classList.toggle('hidden', i !== currentIndex);
                });
                
                indicators.forEach((indicator, i) => {
                    indicator.classList.toggle('active', i === currentIndex);
                });
            }

            function nextSlide() { showSlide(currentIndex + 1); }
            function prevSlide() { showSlide(currentIndex - 1); }

            if (nextButton) nextButton.addEventListener('click', nextSlide);
            if (prevButton) prevButton.addEventListener('click', prevSlide);
            
            indicators.forEach((indicator, index) => {
                indicator.addEventListener('click', () => showSlide(index));
            });

            setInterval(nextSlide, 5000);
            
            if (slides.length > 0) showSlide(0);
        });
    }

    // ==================== 通用功能 ====================

    // 点击外部关闭菜单
    function initOutsideClick() {
        document.addEventListener('click', function(e) {
            const desktopNav = document.querySelector('[data-type="desktop-navigation"]');
            const mobileNav = getMobileNavigation();
            
            // 关闭桌面端菜单
            if (desktopNav && !desktopNav.contains(e.target)) {
                closeAllMenus();
            }
            
            // 关闭移动端菜单（点击外部）
            const trigger = getMobileMenuTrigger();
            if (mobileMenuOpen && mobileNav && !mobileNav.contains(e.target) && (!trigger || !trigger.contains(e.target))) {
                toggleMobileMenu();
            }
            
            // 关闭搜索框（点击外部）
            const searchTriggerContainer = getSearchTriggerContainer();
            const searchForm = getSearchForm();
            const searchTrigger = getSearchTrigger();
            
            if (searchTriggerContainer && searchForm && searchTrigger) {
                const isSearchOpen = searchTriggerContainer.classList.contains('active');
                
                if (isSearchOpen && !searchForm.contains(e.target) && !searchTrigger.contains(e.target)) {
                    toggleSearch();
                }
            }
        });
    }

    // ESC键关闭菜单
    function initEscapeKey() {
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' || e.key === 'Esc') {
                closeAllMenus();
                closeSearch();
                if (mobileMenuOpen) {
                    toggleMobileMenu();
                }
            }
        });
    }

    // 响应式处理
    function initResponsive() {
        window.addEventListener('resize', function() {
            // 如果屏幕宽度超过移动端阈值，关闭移动端菜单
            if (window.innerWidth >= 1280) {
                if (mobileMenuOpen) {
                    toggleMobileMenu();
                }
            }
        });
    }

    // 防止重复初始化
    let initialized = false;

    // 初始化所有功能
    function init() {
        if (initialized) {
            return;
        }
        initialized = true;
        
        initMenuEvents();
        initMobileMenu();
        initTabs();
        initCarousel();
        initSearch();
        initOutsideClick();
        initEscapeKey();
        initResponsive();
    }

    // DOM加载完成后初始化
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

})();