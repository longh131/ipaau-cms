/**
 * 菜单交互脚本 - 原生JavaScript实现
 * 实现与React版本相同的菜单点击下拉效果
 */

(function() {
    'use strict';

    // 状态管理
    let activeMenuIndex = null;
    let activeSubMenuIndex = -1;

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

    // 初始化菜单事件
    function initMenuEvents() {
        const menuItems = getMenuItems();

        menuItems.forEach((menuItem, index) => {
            const menuWrapper = menuItem.querySelector('[data-type="menu-wrapper-0"]');
            const link = menuWrapper.querySelector('a');

            if (link) {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    toggleMenu(menuItem, index);
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

    // 点击外部关闭菜单
    function initOutsideClick() {
        document.addEventListener('click', function(e) {
            const desktopNav = document.querySelector('[data-type="desktop-navigation"]');
            if (desktopNav && !desktopNav.contains(e.target)) {
                closeAllMenus();
            }
        });
    }

    // ESC键关闭菜单
    function initEscapeKey() {
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' || e.key === 'Esc') {
                closeAllMenus();
            }
        });
    }

    // 移动端菜单切换
    function initMobileMenu() {
        const mobileNavTrigger = document.querySelector('[data-type="mobile-nav-trigger"]');
        const mobileNavWrapper = document.querySelector('[data-type="mobile-navigation-wrapper"]');

        if (mobileNavTrigger && mobileNavWrapper) {
            mobileNavTrigger.addEventListener('click', function() {
                mobileNavTrigger.classList.toggle('active');
                mobileNavWrapper.classList.toggle('active');
            });
        }
    }

    // 初始化所有功能
    function init() {
        initMenuEvents();
        initOutsideClick();
        initEscapeKey();
        initMobileMenu();
    }

    // DOM加载完成后初始化
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

})();