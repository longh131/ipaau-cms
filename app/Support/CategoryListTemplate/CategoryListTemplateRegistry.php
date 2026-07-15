<?php

namespace App\Support\CategoryListTemplate;

use App\Models\Category;

class CategoryListTemplateRegistry
{
    public const TEMPLATE_SIMPLE = 'simple';

    public const TEMPLATE_NEWS_CARDS = 'news_cards';

    public const TEMPLATE_TOPICS_ARTICLE_LIST = 'topics_article_list';

    public const DEFAULT_PER_PAGE = 12;

    public const TOPICS_ARTICLE_LIST_PER_PAGE = 24;

    public const TOPICS_ARTICLE_LIST_INITIAL_VISIBLE = 6;

    /** @var array<string, string> */
    public const OPTIONS = [
        self::TEMPLATE_SIMPLE => '简单列表',
        self::TEMPLATE_NEWS_CARDS => '新闻卡片',
        self::TEMPLATE_TOPICS_ARTICLE_LIST => '列表（含：专业技术，数字咨询，会刊精选）',
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

    public static function perPageFor(Category $category): int
    {
        return match (self::resolve($category)) {
            self::TEMPLATE_TOPICS_ARTICLE_LIST => self::TOPICS_ARTICLE_LIST_PER_PAGE,
            default => self::DEFAULT_PER_PAGE,
        };
    }

    public static function initialVisibleFor(Category $category): int
    {
        return match (self::resolve($category)) {
            self::TEMPLATE_TOPICS_ARTICLE_LIST => self::TOPICS_ARTICLE_LIST_INITIAL_VISIBLE,
            default => self::DEFAULT_PER_PAGE,
        };
    }

    public static function usesViewMoreFor(Category $category): bool
    {
        return self::resolve($category) === self::TEMPLATE_TOPICS_ARTICLE_LIST;
    }
}
