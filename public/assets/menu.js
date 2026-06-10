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

        // 关闭所有菜单
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
            const link = menuWrapper.querySelector('a');

            if (link) {
                link.addEventListener('click', function(e) {
                    // 只在桌面端处理点击
                    if (window.innerWidth >= 1280) {
                        e.preventDefault();
                        toggleMenu(menuItem, index);
                    }
                });
            }

            // 子菜单点击事件
            const subMenuItems = getSubMenuItems(menuItem);
            subMenuItems.forEach((subMenuItem, subIndex) => {
                const subMenuWrapper = subMenuItem.querySelector('[data-type="menu-wrapper-1"]');
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

        // 移动端菜单项点击事件（展开子菜单）
        const mobileMenuItems = document.querySelectorAll('[data-type="mobile-navigation"] [data-level="0"]');
        mobileMenuItems.forEach((menuItem) => {
            const button = menuItem.querySelector('button');
            if (button) {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    // 切换子菜单显示
                    menuItem.classList.toggle('active');
                    menuItem.classList.toggle('inactive');
                    
                    // 切换子菜单面板
                    const panel = menuItem.querySelector('[data-type="megamenu-panel"]');
                    if (panel) {
                        panel.classList.toggle('hidden');
                        panel.classList.toggle('block');
                    }
                });
            }
        });
    }

    // ==================== 选项卡效果 ====================

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
                    
                    // 移除所有tab的激活状态
                    tabs.forEach(t => t.classList.remove('active'));
                    panels.forEach(p => p.classList.remove('active'));
                    
                    // 激活当前tab和面板
                    tab.classList.add('active');
                    if (panels[index]) {
                        panels[index].classList.add('active');
                    }
                });
            });
        });

        // 特殊处理tabbedContent区域的选项卡
        const tabbedSections = document.querySelectorAll('section[data-type="tabbedContent"]');
        
        tabbedSections.forEach(section => {
            const tabs = section.querySelectorAll('.tab');
            const contents = section.querySelectorAll('[data-type="tab-content"]');
            
            tabs.forEach((tab, index) => {
                tab.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    // 移除所有tab的激活状态
                    tabs.forEach(t => t.classList.remove('active'));
                    contents.forEach(c => c.classList.remove('active'));
                    
                    // 激活当前tab和内容
                    tab.classList.add('active');
                    if (contents[index]) {
                        contents[index].classList.add('active');
                    }
                });
            });
        });

        // 特殊处理EVENTS COURSES ONLINE CPD选项卡（通过button标签）
        const eventTabContainers = document.querySelectorAll('[data-type="tab-container"]');
        
        eventTabContainers.forEach(container => {
            const tabs = container.querySelectorAll('button[data-type="tab"], .tab button');
            const panels = container.querySelectorAll('[data-type="tab-panel"], [data-type="course-content"]');
            
            tabs.forEach((tab, index) => {
                tab.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    // 移除所有tab的激活状态
                    tabs.forEach(t => t.classList.remove('active'));
                    panels.forEach(p => p.classList.remove('active'));
                    
                    // 激活当前tab和面板
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
        // 处理testimonialCarousel
        const testimonialSections = document.querySelectorAll('section[data-type="testimonialCarousel"]');
        
        testimonialSections.forEach(section => {
            const slides = section.querySelectorAll('[data-type="slide"], .testimonial-slide');
            const prevButton = section.querySelector('[data-type="prev-button"], .prev-button');
            const nextButton = section.querySelector('[data-type="next-button"], .next-button');
            const indicators = section.querySelectorAll('[data-type="indicator"], .carousel-indicator');
            let currentIndex = 0;

            // 显示指定slide
            function showSlide(index) {
                // 循环处理
                if (index < 0) index = slides.length - 1;
                if (index >= slides.length) index = 0;
                
                currentIndex = index;
                
                // 隐藏所有slides
                slides.forEach((slide, i) => {
                    slide.classList.remove('active');
                    slide.classList.add('hidden');
                    if (i === currentIndex) {
                        slide.classList.remove('hidden');
                        slide.classList.add('active');
                    }
                });
                
                // 更新指示器
                indicators.forEach((indicator, i) => {
                    indicator.classList.toggle('active', i === currentIndex);
                });
            }

            // 下一个slide
            function nextSlide() {
                showSlide(currentIndex + 1);
            }

            // 上一个slide
            function prevSlide() {
                showSlide(currentIndex - 1);
            }

            // 添加按钮事件
            if (nextButton) {
                nextButton.addEventListener('click', nextSlide);
            }
            if (prevButton) {
                prevButton.addEventListener('click', prevSlide);
            }

            // 添加指示器事件
            indicators.forEach((indicator, index) => {
                indicator.addEventListener('click', () => showSlide(index));
            });

            // 自动播放
            const interval = setInterval(nextSlide, 5000);
            
            // 鼠标悬停时暂停自动播放
            section.addEventListener('mouseenter', () => clearInterval(interval));
            section.addEventListener('mouseleave', () => setInterval(nextSlide, 5000));

            // 初始化显示第一个slide
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
        });
    }

    // ESC键关闭菜单
    function initEscapeKey() {
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' || e.key === 'Esc') {
                closeAllMenus();
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

    // 初始化所有功能
    function init() {
        initMenuEvents();
        initMobileMenu();
        initTabs();
        initCarousel();
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