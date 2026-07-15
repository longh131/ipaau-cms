<?php

namespace App\Support;

use App\Models\Article;
use Illuminate\Support\Str;

class ArticleSlug
{
    public static function fromTitle(string $title, ?int $ignoreId = null): string
    {
        $title = trim($title);

        if ($title === '') {
            return '';
        }

        $base = Str::slug($title);

        if ($base === '') {
            $base = 'article-'.substr(md5($title), 0, 10);
        }

        return static::ensureUnique($base, $ignoreId);
    }

    public static function ensureUnique(string $base, ?int $ignoreId = null): string
    {
        $slug = $base;
        $suffix = 2;

        while (static::exists($slug, $ignoreId)) {
            $slug = $base.'-'.$suffix;
            $suffix++;
        }

        return $slug;
    }

    private static function exists(string $slug, ?int $ignoreId): bool
    {
        return Article::withTrashed()
            ->when(
                $ignoreId !== null,
                fn ($query) => $query->where('id', '!=', $ignoreId),
            )
            ->where('slug', $slug)
            ->exists();
    }
}
