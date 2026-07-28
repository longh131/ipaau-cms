<?php

namespace App\Support;

use App\Models\Course;

class CourseCategoryClassifier
{
    public static function resolve(?string $title): ?int
    {
        $title = trim(html_entity_decode((string) $title, ENT_QUOTES | ENT_HTML5, 'UTF-8'));

        if ($title === '' || ! preg_match('/【([^】]+)】/u', $title, $matches)) {
            return null;
        }

        $tag = $matches[1];

        if (str_contains($tag, '英文录播') || str_contains($tag, '英文在线')) {
            return Course::CATEGORY_ENGLISH_ONLINE;
        }

        if (str_contains($tag, '在线')) {
            return Course::CATEGORY_CHINESE_LIVE;
        }

        if (str_contains($tag, '公开课')) {
            return Course::CATEGORY_PUBLIC;
        }

        return Course::CATEGORY_CHINESE_OFFLINE;
    }
}
