<?php

namespace App\Support;

use App\Models\Category;

class CategoryIntroduction
{
    public static function forForm(mixed $introduction): ?string
    {
        if (blank($introduction)) {
            return null;
        }

        return RichContent::encodeDocumentForForm($introduction);
    }

    public static function forStorage(mixed $introduction): ?string
    {
        if (blank($introduction)) {
            return null;
        }

        $encoded = RichContent::encodeDocumentForForm($introduction);

        if (blank($encoded) || $encoded === '{"type":"doc","content":[]}') {
            return null;
        }

        return $encoded;
    }

    public static function toHtml(mixed $categoryOrState): string
    {
        $state = $categoryOrState instanceof Category
            ? $categoryOrState->introduction
            : $categoryOrState;

        return RichContent::toHtml($state ?? '');
    }

    public static function hasVisibleHtml(mixed $categoryOrState): bool
    {
        return RichContent::hasVisibleHtml(self::toHtml($categoryOrState));
    }
}
