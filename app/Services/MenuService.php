<?php

namespace App\Services;

use App\Support\MediaUrl;

use App\Models\Menu;
use App\Models\MenuItem;
use App\Support\MenuItemLink;
use Illuminate\Support\Collection;

class MenuService
{
    /** 对应后台「菜单管理 → 顶部菜单」的 location 标识 */
    public const HEADER_LOCATION = 'top';

    /** 对应后台「菜单管理 → 底部菜单」的 location 标识 */
    public const FOOTER_LOCATION = 'bottom';

    public function getHeaderMenuItems(): array
    {
        return $this->getMenuItemsByLocation(self::HEADER_LOCATION, withPromo: true);
    }

    public function getFooterMenuItems(): array
    {
        return $this->getMenuItemsByLocation(self::FOOTER_LOCATION, withPromo: false);
    }

    private function getMenuItemsByLocation(string $location, bool $withPromo): array
    {
        $menu = Menu::query()
            ->where('location', $location)
            ->where('is_active', true)
            ->first();

        if (! $menu) {
            return [];
        }

        $roots = MenuItem::query()
            ->where('menu_id', $menu->id)
            ->where('is_active', true)
            ->whereNull('parent_id')
            ->orderBy('sort_order')
            ->get();

        return $this->buildTree($roots, $withPromo);
    }

    /**
     * @param  Collection<int, MenuItem>  $items
     */
    private function buildTree(Collection $items, bool $withPromo = true): array
    {
        return $items->map(fn (MenuItem $item) => $this->transformItem($item, $withPromo))->values()->all();
    }

    private function transformItem(MenuItem $item, bool $withPromo = true): array
    {
        $children = MenuItem::query()
            ->where('parent_id', $item->id)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $data = [
            'id' => $item->id,
            'title' => $item->title,
            'url' => MenuItemLink::resolveUrl($item),
            'target' => $item->target ?? '_self',
            'children' => $this->buildTree($children, $withPromo),
        ];

        if ($withPromo) {
            $data['promo'] = $this->promoFor($item);
        }

        return $data;
    }

    private function promoFor(MenuItem $item): array
    {
        $promoUrl = $this->resolveLinkUrl($item->megamenu_promo_url);

        return [
            'image' => MediaUrl::resolve($this->normalizeStoredPath($item->icon)),
            'image_alt' => $item->megamenu_image_alt ?: $item->title,
            'text' => $item->megamenu_promo_text,
            'url' => $promoUrl,
            'target' => $this->linkTarget($promoUrl, $item->target ?? '_self'),
        ];
    }

    private function normalizeStoredPath(mixed $path): ?string
    {
        if (blank($path)) {
            return null;
        }

        $normalized = MediaUrl::normalizeStoredPath($path);

        return $normalized !== '' ? $normalized : null;
    }

    private function resolveLinkUrl(?string $url): ?string
    {
        if (blank($url)) {
            return null;
        }

        if (str_starts_with($url, 'http://') || str_starts_with($url, 'https://') || str_starts_with($url, '//')) {
            return $url;
        }

        if (str_starts_with($url, '/')) {
            return url($url);
        }

        return url('/'.ltrim($url, '/'));
    }

    private function linkTarget(?string $url, string $default): string
    {
        if (blank($url)) {
            return $default;
        }

        if (str_starts_with($url, 'http://') || str_starts_with($url, 'https://') || str_starts_with($url, '//')) {
            return '_blank';
        }

        return $default;
    }
}
