<style>
    .menu-item-toggle.is-expanded .menu-item-toggle-icon {
        transform: rotate(0deg);
    }

    .menu-item-toggle:not(.is-expanded) .menu-item-toggle-icon {
        transform: rotate(-90deg);
    }
</style>

<script>
    (function () {
        const collapsed = new Set();

        function collectMenuItems() {
            return [...document.querySelectorAll('.menu-item-title')].map((el) => ({
                id: el.dataset.id,
                parentId: el.dataset.parentId || '',
                depth: parseInt(el.dataset.depth, 10) || 0,
                row: el.closest('tr'),
            })).filter((item) => item.row);
        }

        function itemHasChildren(items, id) {
            return items.some((item) => item.parentId == id);
        }

        function isRowVisible(item, collapsedIds) {
            let parentId = item.parentId;

            while (parentId) {
                if (collapsedIds.has(parentId)) {
                    return false;
                }

                const parentEl = document.querySelector(`.menu-item-title[data-id="${parentId}"]`);
                parentId = parentEl?.dataset.parentId || '';
            }

            return true;
        }

        function syncToggleIcons() {
            document.querySelectorAll('.menu-item-toggle').forEach((button) => {
                const expanded = ! collapsed.has(button.dataset.id);
                button.classList.toggle('is-expanded', expanded);
            });
        }

        function applyMenuTreeVisibility() {
            const items = collectMenuItems();

            items.forEach((item) => {
                item.row.style.display = isRowVisible(item, collapsed) ? '' : 'none';
            });

            syncToggleIcons();
        }

        window.toggleMenuItem = function (event, button) {
            event.preventDefault();
            event.stopPropagation();

            const id = button.dataset.id;

            if (collapsed.has(id)) {
                collapsed.delete(id);
            } else {
                collapsed.add(id);
            }

            applyMenuTreeVisibility();
        };

        window.initMenuItemTree = function () {
            collapsed.clear();

            const items = collectMenuItems();

            items.forEach((item) => {
                if (itemHasChildren(items, item.id) && item.depth < 2) {
                    collapsed.add(item.id);
                }
            });

            applyMenuTreeVisibility();
        };

        document.addEventListener('DOMContentLoaded', initMenuItemTree);
        document.addEventListener('livewire:navigated', initMenuItemTree);

        if (typeof Livewire !== 'undefined') {
            Livewire.hook('commit', ({ succeed }) => {
                succeed(() => {
                    queueMicrotask(initMenuItemTree);
                });
            });
        }
    })();
</script>
