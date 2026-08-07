<?php

namespace App\Support\CategoryListTemplate;

use App\Models\Article;
use App\Models\Category;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class VideoHubSectionData
{
    public const BROADCAST_CATEGORY_SLUG = 'ipa-broadcast';

    public const RECAP_CATEGORY_SLUG = 'ipa-event-recap';

    public const FEATURED_LIMIT = 6;

    /**
     * @return array<int, array{
     *     title: string,
     *     slug: string,
     *     more_url: string,
     *     articles: Collection<int, Article>
     * }>
     */
    public static function sectionsForFrontend(): array
    {
        return [
            self::sectionForSlug('IPA播报', self::BROADCAST_CATEGORY_SLUG),
            self::sectionForSlug('IPA活动回顾', self::RECAP_CATEGORY_SLUG),
        ];
    }

    /**
     * @return array{title: string, slug: string, more_url: string, articles: Collection<int, Article>}
     */
    private static function sectionForSlug(string $title, string $categorySlug): array
    {
        $category = Category::query()
            ->where('slug', $categorySlug)
            ->where('is_active', true)
            ->first();

        $articles = collect();

        if ($category !== null) {
            $articles = self::applyPriorityOrdering(
                Article::query()
                    ->where('category_id', $category->id)
                    ->where('is_active', true),
            )
                ->limit(self::FEATURED_LIMIT)
                ->get();
        }

        return [
            'title' => $title,
            'slug' => $categorySlug,
            'more_url' => route('category.show', $categorySlug),
            'articles' => $articles,
        ];
    }

    /**
     * @param  Builder<Article>  $query
     * @return Builder<Article>
     */
    public static function applyPriorityOrdering(Builder $query): Builder
    {
        return $query
            ->orderByDesc('sort_order')
            ->orderByDesc('published_at')
            ->orderByDesc('id');
    }
}
