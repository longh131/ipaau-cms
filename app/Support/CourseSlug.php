<?php

namespace App\Support;

use App\Models\Course;
use Illuminate\Support\Str;

class CourseSlug
{
    public static function fromTitle(string $title, ?int $ignoreId = null): string
    {
        $title = trim(html_entity_decode($title, ENT_QUOTES | ENT_HTML5, 'UTF-8'));

        if ($title === '') {
            return '';
        }

        $base = Str::slug($title);

        if ($base === '') {
            $base = 'course-'.substr(md5($title), 0, 10);
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
        return Course::query()
            ->when(
                $ignoreId !== null,
                fn ($query) => $query->whereKeyNot($ignoreId),
            )
            ->where('slug', $slug)
            ->exists();
    }
}
