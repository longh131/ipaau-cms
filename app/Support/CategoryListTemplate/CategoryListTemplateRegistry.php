<?php

namespace App\Support\CategoryListTemplate;

use App\Models\Category;
use Illuminate\Database\Eloquent\Builder;

class CategoryListTemplateRegistry
{
    public const TEMPLATE_SIMPLE = 'simple';

    public const TEMPLATE_NEWS_CARDS = 'news_cards';

    public const TEMPLATE_TOPICS_ARTICLE_LIST = 'topics_article_list';

    public const TEMPLATE_TEAM_INTRO = 'team_intro';

    public const TEMPLATE_COURSE_TABLE = 'course_table';

    public const TEMPLATE_SPECIAL_COURSE_LIST = 'special_course_list';

    public const TEMPLATE_SPECIAL_CERTIFICATE_LOOKUP = 'special_certificate_lookup';

    public const DEFAULT_PER_PAGE = 12;

    public const COURSE_TABLE_PER_PAGE = 20;

    public const TOPICS_ARTICLE_LIST_PER_PAGE = 24;

    public const TOPICS_ARTICLE_LIST_INITIAL_VISIBLE = 6;

    /** @var array<string, string> */
    public const OPTIONS = [
        self::TEMPLATE_SIMPLE => '简单列表',
        self::TEMPLATE_NEWS_CARDS => '新闻卡片',
        self::TEMPLATE_TOPICS_ARTICLE_LIST => '列表（含：专业技术，数字咨询，会刊精选）',
        self::TEMPLATE_TEAM_INTRO => '团队介绍',
        self::TEMPLATE_COURSE_TABLE => '课程表格',
        self::TEMPLATE_SPECIAL_COURSE_LIST => '功能栏目页（课程汇总）',
        self::TEMPLATE_SPECIAL_CERTIFICATE_LOOKUP => '功能栏目页（证书查询）',
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
            self::TEMPLATE_COURSE_TABLE, self::TEMPLATE_SPECIAL_COURSE_LIST => self::COURSE_TABLE_PER_PAGE,
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

    public static function isTeamIntro(Category $category): bool
    {
        return self::resolve($category) === self::TEMPLATE_TEAM_INTRO;
    }

    public static function isCourseTable(Category $category): bool
    {
        return self::resolve($category) === self::TEMPLATE_COURSE_TABLE;
    }

    public static function isSpecialCourseList(Category $category): bool
    {
        return self::resolve($category) === self::TEMPLATE_SPECIAL_COURSE_LIST;
    }

    public static function isSpecialCertificateLookup(Category $category): bool
    {
        return self::resolve($category) === self::TEMPLATE_SPECIAL_CERTIFICATE_LOOKUP;
    }

    /**
     * @param  Builder<\App\Models\Article>  $query
     */
    public static function applyArticleOrdering(Builder $query, Category $category): Builder
    {
        if (self::isTeamIntro($category)) {
            return $query
                ->orderBy('sort_order')
                ->orderByDesc('published_at')
                ->orderBy('id');
        }

        return $query
            ->orderByDesc('is_sticky')
            ->orderByDesc('published_at')
            ->orderByDesc('sort_order');
    }
}
