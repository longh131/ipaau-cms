<?php

namespace App\Support;

use App\Models\Article;
use App\Models\Category;
use App\Models\MenuItem;
use App\Models\Page;

/**
 * 菜单项链接规范（与前台路由命名一致，供后台保存与未来前台解析共用）。
 *
 * URL 规范：
 * - 单页栏目：GET /category/{slug}  → route: category.show（正文来自 pages 表）
 * - 文章栏目：GET /category/{slug}  → route: category.show（文章列表）
 * - 文章：GET /article/{slug}       → route: article.show
 * - 外链：menu_items.url 存完整 URL
 * - /page/{slug} 保留兼容，301 跳转到 /category/{slug}
 */
class MenuItemLink
{
    public const TYPE_URL = 'url';

    public const TYPE_PAGE = 'page';

    public const TYPE_CATEGORY = 'category';

    public const TYPE_ARTICLE = 'article';

    public const ROUTE_MAP = [
        self::TYPE_PAGE => 'category.show',
        self::TYPE_CATEGORY => 'category.show',
        self::TYPE_ARTICLE => 'article.show',
    ];

    public static function typeOptions(): array
    {
        return [
            self::TYPE_URL => '自定义 URL',
            self::TYPE_PAGE => '单页',
            self::TYPE_CATEGORY => '栏目',
            self::TYPE_ARTICLE => '文章',
        ];
    }

    public static function inferType(MenuItem $item): string
    {
        if ($item->url) {
            return self::TYPE_URL;
        }

        $slug = self::slugFromParams($item->route_params);

        return match ($item->route) {
            'page.show', 'category.show' => filled(Page::where('slug', $slug)->value('id'))
                ? self::TYPE_PAGE
                : self::TYPE_CATEGORY,
            self::ROUTE_MAP[self::TYPE_ARTICLE] => self::TYPE_ARTICLE,
            default => self::TYPE_URL,
        };
    }

    public static function inferLinkId(MenuItem $item): ?int
    {
        $slug = self::slugFromParams($item->route_params);

        if (! $slug) {
            return null;
        }

        return match ($item->route) {
            'page.show', 'category.show' => Page::where('slug', $slug)->value('id')
                ?: Category::where('slug', $slug)->value('id'),
            self::ROUTE_MAP[self::TYPE_ARTICLE] => Article::where('slug', $slug)->value('id'),
            default => null,
        };
    }

    public static function apply(array $data): array
    {
        $linkType = $data['link_type'] ?? self::TYPE_URL;
        $linkId = $data['link_id'] ?? null;
        unset($data['link_type'], $data['link_id']);

        if ($linkType === self::TYPE_URL) {
            $data['route'] = null;
            $data['route_params'] = null;

            return $data;
        }

        $slug = self::resolveSlug($linkType, $linkId);

        $data['url'] = null;
        $data['route'] = self::ROUTE_MAP[$linkType] ?? null;
        $data['route_params'] = $slug ? json_encode(['slug' => $slug], JSON_UNESCAPED_UNICODE) : null;

        return $data;
    }

    public static function resolveUrl(MenuItem $item): string
    {
        if ($item->url) {
            return $item->url;
        }

        if (! $item->route) {
            return '#';
        }

        $params = self::paramsFromStored($item->route_params);

        try {
            return route($item->route, $params);
        } catch (\Throwable) {
            return '#';
        }
    }

    private static function resolveSlug(string $linkType, ?int $linkId): ?string
    {
        if (! $linkId) {
            return null;
        }

        return match ($linkType) {
            self::TYPE_PAGE => Page::whereKey($linkId)->value('slug'),
            self::TYPE_CATEGORY => Category::whereKey($linkId)->value('slug'),
            self::TYPE_ARTICLE => Article::whereKey($linkId)->value('slug'),
            default => null,
        };
    }

    private static function slugFromParams(?string $routeParams): ?string
    {
        $params = self::paramsFromStored($routeParams);

        return $params['slug'] ?? null;
    }

    private static function paramsFromStored(?string $routeParams): array
    {
        if (! $routeParams) {
            return [];
        }

        $decoded = json_decode($routeParams, true);

        return is_array($decoded) ? $decoded : [];
    }
}
