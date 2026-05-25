<?php

namespace App\Helpers;

use App\Models\Category;

class NavigationHelper
{
    public static function getMainNavigation()
    {
        $categories = Category::where('parent_id', 0)
            ->orderBy('sort_order')
            ->with('children')
            ->get();

        return $categories->map(function ($category) {
            return [
                'name' => $category->name,
                'url' => '/category/' . $category->slug,
                'children' => $category->children->map(function ($child) {
                    return [
                        'name' => $child->name,
                        'url' => '/category/' . $child->slug,
                    ];
                })->toArray()
            ];
        })->toArray();
    }
}