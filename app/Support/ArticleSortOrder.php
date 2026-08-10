<?php

namespace App\Support;

use App\Models\Article;
use Illuminate\Database\Eloquent\Builder;

class ArticleSortOrder
{
    /**
     * 全站文章列表通用排序：置顶 → sort_order 降序 → published_at 降序 → id 降序。
     *
     * @param  Builder<Article>  $query
     * @return Builder<Article>
     */
    public static function applyDefaultOrdering(Builder $query): Builder
    {
        return $query
            ->orderByDesc('is_sticky')
            ->orderByDesc('sort_order')
            ->orderByDesc('published_at')
            ->orderByDesc('id');
    }

    /**
     * 按导入先后（id 升序 ≈ 旧站列表从上到下）镜像 sort_order，
     * 使较新的条目在「sort_order 越大越靠前」规则下排在前面。
     */
    public static function mirrorImportOrderForCategory(int $categoryId): int
    {
        $articles = Article::query()
            ->where('category_id', $categoryId)
            ->orderBy('id')
            ->get(['id', 'sort_order']);

        if ($articles->isEmpty()) {
            return 0;
        }

        $minId = (int) $articles->min('id');
        $maxId = (int) $articles->max('id');
        $updated = 0;

        foreach ($articles as $article) {
            $newSortOrder = $maxId + $minId - (int) $article->id;

            if ((int) $article->sort_order !== $newSortOrder) {
                $article->updateQuietly(['sort_order' => $newSortOrder]);
                $updated++;
            }
        }

        return $updated;
    }

    public static function syncToId(Article $article): void
    {
        $id = (int) $article->getKey();

        if ($id <= 0 || (int) $article->sort_order === $id) {
            return;
        }

        $article->updateQuietly(['sort_order' => $id]);
    }
}
