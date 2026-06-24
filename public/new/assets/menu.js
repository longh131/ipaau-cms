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

        // 移动端菜单项点击事件（展开二级菜单）
        const mobileMenuItems = document.querySelectorAll('[data-type="mobile-navigation"] [data-level="0"]');
        mobileMenuItems.forEach((menuItem) => {
            const button = menuItem.querySelector('button');
            if (button) {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    menuItem.classList.toggle('inactive');
                    menuItem.classList.toggle('active');
                    const panel = menuItem.querySelector('[data-type="megamenu-panel"]');
                    if (panel) {
                        panel.classList.toggle('hidden');
                        panel.classList.toggle('block');
                    }
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

        // 特殊处理tabbedContent区域的选项卡
        const tabbedSections = document.querySelectorAll('section[data-type="tabbedContent"]');
        
        tabbedSections.forEach(section => {
            const tabs = section.querySelectorAll('.tab');
            const contents = section.querySelectorAll('[data-type="tab-content"]');
            
            tabs.forEach((tab, index) => {
                tab.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    tabs.forEach(t => t.classList.remove('active'));
                    contents.forEach(c => c.classList.remove('active'));
                    
                    tab.classList.add('active');
                    if (contents[index]) {
                        contents[index].classList.add('active');
                    }
                });
            });
        });

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

        // 处理简单的.tab类选项卡（EVENTS COURSES ONLINE CPD）
        console.log('[DEBUG] 开始处理简单选项卡');
        const simpleTabContainers = document.querySelectorAll('.flex.h-full.flex-col.items-start');
        console.log('[DEBUG] 找到简单选项卡容器数量:', simpleTabContainers.length);
        
        simpleTabContainers.forEach((container, containerIndex) => {
            const tabs = container.querySelectorAll('.tab');
            console.log('[DEBUG] 容器', containerIndex, '找到.tab按钮数量:', tabs.length);
            
            if (tabs.length > 0) {
                // 找到按钮容器
                const buttonContainer = container.querySelector('.flex.flex-wrap.gap-4');
                console.log('[DEBUG] 容器', containerIndex, '按钮容器:', buttonContainer ? '找到' : '未找到');
                
                if (buttonContainer) {
                    // 方式1：查找按钮容器内部的内容区域
                    let contentContainer = buttonContainer.querySelector('.space-y-8');
                    console.log('[DEBUG] 容器', containerIndex, '按钮容器内查找space-y-8:', contentContainer ? '找到' : '未找到');
                    
                    // 方式2：如果不在按钮容器内，查找整个父容器内的内容区域
                    if (!contentContainer) {
                        contentContainer = container.querySelector('.space-y-8');
                        console.log('[DEBUG] 容器', containerIndex, '父容器内查找space-y-8:', contentContainer ? '找到' : '未找到');
                    }
                    
                    // 方式3：查找按钮容器之后的所有元素中包含space-y-8的
                    if (!contentContainer) {
                        const allElements = container.querySelectorAll('.space-y-8');
                        if (allElements.length > 0) {
                            contentContainer = allElements[0];
                            console.log('[DEBUG] 容器', containerIndex, '查询选择器查找space-y-8:', '找到');
                        }
                    }
                    
                    // 方式4：查找包含tab-panel类的元素的父容器
                    if (!contentContainer) {
                        const tabPanel = container.querySelector('.tab-panel, [data-type="tab-panel"]');
                        if (tabPanel) {
                            contentContainer = tabPanel.parentElement;
                            console.log('[DEBUG] 容器', containerIndex, '通过tab-panel父容器查找:', contentContainer.className);
                        }
                    }
                    
                    if (contentContainer) {
                        console.log('[DEBUG] 容器', containerIndex, '找到内容容器:', contentContainer.className);
                        // 查找内容容器下的直接子元素作为面板
                        let panels = contentContainer.children;
                        console.log('[DEBUG] 容器', containerIndex, '找到面板数量:', panels.length);
                        
                        // 如果面板数量少于tabs数量，动态创建缺失的面板
                        if (panels.length < tabs.length) {
                            console.log('[DEBUG] 容器', containerIndex, '面板数量不足，需要创建', tabs.length - panels.length, '个面板');
                            
                            // 将HTMLCollection转换为数组
                            panels = Array.from(panels);
                            
                            // 获取第一个面板的HTML作为模板
                            const firstPanel = panels[0];
                            if (firstPanel) {
                                const panelTemplate = firstPanel.outerHTML;
                                
                                for (let i = panels.length; i < tabs.length; i++) {
                                    const newPanel = document.createElement('div');
                                    newPanel.innerHTML = panelTemplate;
                                    const newPanelContent = newPanel.firstChild;
                                    newPanelContent.classList.add('hidden');
                                    
                                    // 更新内容为对应tab的名称
                                    const tabName = tabs[i].textContent.trim();
                                    const titleElement = newPanelContent.querySelector('h3, h4, .title');
                                    const descElement = newPanelContent.querySelector('p, .description');
                                    if (titleElement) {
                                        titleElement.textContent = `${tabName} Content`;
                                    }
                                    if (descElement) {
                                        descElement.textContent = `This is the ${tabName.toLowerCase()} section. Discover our comprehensive ${tabName.toLowerCase()} offerings designed to support your professional development and enhance your skills.`;
                                    }
                                    
                                    contentContainer.appendChild(newPanelContent);
                                    panels.push(newPanelContent);
                                    console.log('[DEBUG] 容器', containerIndex, '创建面板', i);
                                }
                            }
                        }
                        
                        const targetPanels = panels;
                        
                        // 查找选项卡容器中的图片元素（先在容器内找，找不到就在父容器中找）
                        let imgWrapper = container.querySelector('.img-shape-acorn.img-wrapper');
                        if (!imgWrapper) {
                            imgWrapper = container.parentElement.querySelector('.img-shape-acorn.img-wrapper');
                        }
                        if (!imgWrapper) {
                            imgWrapper = container.closest('.flex, .grid, .section').querySelector('.img-shape-acorn.img-wrapper');
                        }
                        if (!imgWrapper) {
                            // 最后尝试在整个文档中查找
                            imgWrapper = document.querySelector('.img-shape-acorn.img-wrapper');
                        }
                        console.log('[DEBUG] 容器', containerIndex, '找到图片容器:', imgWrapper ? '找到' : '未找到');
                        if (imgWrapper) {
                            console.log('[DEBUG] 容器', containerIndex, '图片容器className:', imgWrapper.className);
                        }
                        
                        // 为每个tab准备不同的图片URL
                        const imageUrls = [
                            'https://picsum.photos/400/400.webp?random=7',
                            'https://picsum.photos/400/400.webp?random=8',
                            'https://picsum.photos/400/400.webp?random=9'
                        ];
                        
                        tabs.forEach((tab, index) => {
                            tab.addEventListener('click', function(e) {
                                e.preventDefault();
                                console.log('[DEBUG] 选项卡点击, index:', index, 'text:', tab.textContent.trim());
                                
                                tabs.forEach(t => t.classList.remove('active'));
                                console.log('[DEBUG] 移除所有tab的active类');
                                
                                // 隐藏所有面板
                                for (let i = 0; i < targetPanels.length; i++) {
                                    targetPanels[i].classList.remove('active');
                                    targetPanels[i].classList.add('hidden');
                                }
                                console.log('[DEBUG] 隐藏所有面板');
                                
                                tab.classList.add('active');
                                console.log('[DEBUG] 激活当前tab');
                                
                                if (targetPanels[index]) {
                                    targetPanels[index].classList.remove('hidden');
                                    targetPanels[index].classList.add('active');
                                    console.log('[DEBUG] 显示面板', index);
                                } else {
                                    console.log('[DEBUG] 面板', index, '不存在');
                                }
                                
                                // 更新右侧图片
                                if (imgWrapper && imageUrls[index]) {
                                    imgWrapper.style.setProperty('--cta-bg-desktop', `url(${imageUrls[index]})`);
                                    console.log('[DEBUG] 更新图片为:', imageUrls[index]);
                                }
                            });
                        });
                    } else {
                        console.log('[DEBUG] 容器', containerIndex, '未找到内容容器');
                        // 尝试直接在父容器中查找面板
                        const directPanels = container.querySelectorAll('.tab-panel, [data-type="tab-panel"], .course-content');
                        console.log('[DEBUG] 容器', containerIndex, '直接查找面板数量:', directPanels.length);
                        
                        if (directPanels.length > 0) {
                            console.log('[DEBUG] 容器', containerIndex, '使用直接查找的面板');
                            tabs.forEach((tab, index) => {
                                tab.addEventListener('click', function(e) {
                                    e.preventDefault();
                                    tabs.forEach(t => t.classList.remove('active'));
                                    directPanels.forEach(p => {
                                        p.classList.remove('active');
                                        p.classList.add('hidden');
                                    });
                                    tab.classList.add('active');
                                    if (directPanels[index]) {
                                        directPanels[index].classList.remove('hidden');
                                        directPanels[index].classList.add('active');
                                    }
                                });
                            });
                        }
                    }
                }
            }
        });
    }

    // ==================== 滑动效果（轮播）====================

    // 初始化滑动效果
    function initCarousel() {
        // 处理.testimonial-carousel类的轮播
        console.log('[DEBUG] 开始处理轮播');
        const testimonialCarousels = document.querySelectorAll('.testimonial-carousel');
        console.log('[DEBUG] 找到轮播容器数量:', testimonialCarousels.length);
        
        testimonialCarousels.forEach((carousel, carouselIndex) => {
            console.log('[DEBUG] 轮播', carouselIndex, '开始初始化');
            
            const swiperWrapper = carousel.querySelector('.swiper-wrapper');
            const slides = carousel.querySelectorAll('[data-type="slide"], .testimonial-slide, .swiper-slide');
            const prevButton = carousel.querySelector('[data-type="prev-button"], .prev-button, .testimonial-card__navigation-previous');
            const nextButton = carousel.querySelector('[data-type="next-button"], .next-button, .testimonial-card__navigation-next');
            const pagination = carousel.querySelector('.swiper-pagination');
            const indicators = carousel.querySelectorAll('[data-type="indicator"], .carousel-indicator, .swiper-pagination-bullet');
            const autoplayButton = carousel.querySelector('.testimonial-card__navigation-autoplay-button');
            
            console.log('[DEBUG] 轮播', carouselIndex, 'slides数量:', slides.length);
            console.log('[DEBUG] 轮播', carouselIndex, 'prevButton:', prevButton ? '找到' : '未找到');
            console.log('[DEBUG] 轮播', carouselIndex, 'nextButton:', nextButton ? '找到' : '未找到');
            console.log('[DEBUG] 轮播', carouselIndex, 'pagination:', pagination ? '找到' : '未找到');
            console.log('[DEBUG] 轮播', carouselIndex, 'indicators数量:', indicators.length);
            console.log('[DEBUG] 轮播', carouselIndex, 'autoplayButton:', autoplayButton ? '找到' : '未找到');
            
            let currentIndex = 0;
            let autoplayInterval = null;
            let isAutoplay = true;

            function showSlide(index) {
                if (index < 0) index = slides.length - 1;
                if (index >= slides.length) index = 0;
                
                currentIndex = index;
                console.log('[DEBUG] 轮播', carouselIndex, '显示slide:', currentIndex);
                
                // 使用transform实现左右滑动
                if (swiperWrapper) {
                    swiperWrapper.style.transform = `translateX(-${currentIndex * 100}%)`;
                    swiperWrapper.style.transition = 'transform 0.5s ease';
                }
                
                slides.forEach((slide, i) => {
                    slide.classList.remove('active', 'swiper-slide-active', 'swiper-slide-prev', 'swiper-slide-next', 'hidden');
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
                    if (i === currentIndex) {
                        indicator.classList.add('active', 'swiper-pagination-bullet-active');
                    }
                });
            }

            function nextSlide() {
                console.log('[DEBUG] 轮播', carouselIndex, '下一个slide');
                showSlide(currentIndex + 1);
            }

            function prevSlide() {
                console.log('[DEBUG] 轮播', carouselIndex, '上一个slide');
                showSlide(currentIndex - 1);
            }

            function startAutoplay() {
                if (autoplayInterval) clearInterval(autoplayInterval);
                autoplayInterval = setInterval(nextSlide, 5000);
                isAutoplay = true;
                console.log('[DEBUG] 轮播', carouselIndex, '启动自动播放');
                if (autoplayButton) {
                    autoplayButton.setAttribute('aria-pressed', 'true');
                    autoplayButton.setAttribute('aria-label', 'Pause autoplay');
                }
            }

            function stopAutoplay() {
                if (autoplayInterval) clearInterval(autoplayInterval);
                autoplayInterval = null;
                isAutoplay = false;
                console.log('[DEBUG] 轮播', carouselIndex, '停止自动播放');
                if (autoplayButton) {
                    autoplayButton.setAttribute('aria-pressed', 'false');
                    autoplayButton.setAttribute('aria-label', 'Play autoplay');
                }
            }

            function toggleAutoplay() {
                console.log('[DEBUG] 轮播', carouselIndex, '切换自动播放状态');
                if (isAutoplay) {
                    stopAutoplay();
                } else {
                    startAutoplay();
                }
            }

            if (nextButton) {
                nextButton.addEventListener('click', function(e) {
                    e.preventDefault();
                    console.log('[DEBUG] 轮播', carouselIndex, '点击next按钮');
                    nextSlide();
                });
            }
            if (prevButton) {
                prevButton.addEventListener('click', function(e) {
                    e.preventDefault();
                    console.log('[DEBUG] 轮播', carouselIndex, '点击prev按钮');
                    prevSlide();
                });
            }

            indicators.forEach((indicator, index) => {
                indicator.addEventListener('click', function(e) {
                    e.preventDefault();
                    console.log('[DEBUG] 轮播', carouselIndex, '点击指示器:', index);
                    showSlide(index);
                });
            });

            if (autoplayButton) {
                autoplayButton.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    console.log('[DEBUG] 轮播', carouselIndex, '点击自动播放按钮');
                    console.log('[DEBUG] 轮播', carouselIndex, '当前isAutoplay状态:', isAutoplay);
                    toggleAutoplay();
                });
                
                // 添加touchend事件支持移动端
                autoplayButton.addEventListener('touchend', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    console.log('[DEBUG] 轮播', carouselIndex, '触摸自动播放按钮');
                    toggleAutoplay();
                });
            }

            carousel.addEventListener('mouseenter', function() {
                console.log('[DEBUG] 轮播', carouselIndex, '鼠标进入，暂停自动播放');
                // 保存当前状态，以便鼠标离开时恢复
                if (isAutoplay) {
                    stopAutoplay();
                    // 标记需要在鼠标离开时恢复
                    carousel.dataset.shouldResume = 'true';
                }
            });
            carousel.addEventListener('mouseleave', function() {
                console.log('[DEBUG] 轮播', carouselIndex, '鼠标离开');
                console.log('[DEBUG] 轮播', carouselIndex, 'shouldResume:', carousel.dataset.shouldResume);
                // 只有当鼠标进入前是自动播放状态时才恢复
                if (carousel.dataset.shouldResume === 'true') {
                    startAutoplay();
                    carousel.dataset.shouldResume = 'false';
                }
            });

            // 设置swiper-wrapper样式
            if (swiperWrapper) {
                swiperWrapper.style.display = 'flex';
                swiperWrapper.style.flexDirection = 'row';
            }
            
            // 设置slides宽度
            slides.forEach(slide => {
                slide.style.flex = '0 0 100%';
                slide.style.width = '100%';
            });

            // 初始化显示第一个slide
            if (slides.length > 0) {
                showSlide(0);
            }

            // 启动自动播放
            startAutoplay();
            console.log('[DEBUG] 轮播', carouselIndex, '初始化完成');
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