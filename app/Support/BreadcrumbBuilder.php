<?php

namespace App\Support;

use App\Models\Category;

class BreadcrumbBuilder
{
    /**
     * @return array<int, array{label: string, url: ?string, is_home: bool, is_current: bool}>
     */
    public static function forCategory(Category $category, ?string $currentLabel = null): array
    {
        $items = [
            [
                'label' => 'Home',
                'url' => route('home'),
                'is_home' => true,
                'is_current' => false,
            ],
        ];

        $chain = static::categoryChain($category);
        $lastIndex = count($chain) - 1;

        foreach ($chain as $index => $chainCategory) {
            $isLast = $index === $lastIndex;
            $label = $isLast && filled($currentLabel)
                ? $currentLabel
                : $chainCategory->name;

            $items[] = [
                'label' => $label,
                'url' => $isLast ? null : route('category.show', $chainCategory->slug),
                'is_home' => false,
                'is_current' => $isLast,
            ];
        }

        return $items;
    }

    /**
     * @return array<int, Category>
     */
    protected static function categoryChain(Category $category): array
    {
        $chain = [];
        $current = $category;

        while ($current) {
            array_unshift($chain, $current);

            if (! $current->parent_id) {
                break;
            }

            $current = Category::query()
                ->whereKey($current->parent_id)
                ->where('is_active', true)
                ->first();

            if (! $current) {
                break;
            }
        }

        return $chain;
    }
}
