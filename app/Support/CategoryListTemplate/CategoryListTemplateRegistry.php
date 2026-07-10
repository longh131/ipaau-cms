<?php

namespace App\Support\CategoryListTemplate;

use App\Models\Category;

class CategoryListTemplateRegistry
{
    public const TEMPLATE_SIMPLE = 'simple';

    public const TEMPLATE_NEWS_CARDS = 'news_cards';

    /** @var array<string, string> */
    public const OPTIONS = [
        self::TEMPLATE_SIMPLE => '简单列表',
        self::TEMPLATE_NEWS_CARDS => '新闻卡片',
    ];

    public static function resolve(Category $category): string
    {
        $template = (string) ($category->list_template ?? '');

        if ($template !== '' && array_key_exists($template, self::OPTIONS)) {
            return $template;
        }

        return self::TEMPLATE_SIMPLE;
    }

    public static function viewFor(Category $category): string
    {
        $template = self::resolve($category);
        $view = 'frontend.categories.'.$template;

        if (! view()->exists($view)) {
            return 'frontend.categories.simple';
        }

        return $view;
    }
}
