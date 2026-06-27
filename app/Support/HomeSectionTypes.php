<?php

namespace App\Support;

use App\Support\HomeSection\AboutIntroSectionData;
use App\Support\HomeSection\CpdIntroSectionData;
use App\Support\HomeSection\CtaSectionData;
use App\Support\HomeSection\DiversitySectionData;
use App\Support\HomeSection\FaqSectionData;
use App\Support\HomeSection\FootnoteCardsSectionData;
use App\Support\HomeSection\HeroSectionData;
use App\Support\HomeSection\MembershipSectionData;
use App\Support\HomeSection\NewsletterSectionData;
use App\Support\HomeSection\StatsSectionData;
use App\Support\HomeSection\TabbedContentSectionData;
use App\Support\HomeSection\TestimonialsSectionData;

/**
 * 首页 Blade section 与 page_components 的映射。
 * 前台接入时按 sort_order 渲染对应 @include('sections.home.{key}')。
 */
class HomeSectionTypes
{
    public const PAGE_SLUG = 'home';

    /** @var array<int, string> */
    public const STRUCTURED_TYPES = [
        'hero',
        'footnote-cards',
        'membership',
        'stats',
        'cpd-intro',
        'tabbed-content',
        'testimonials',
        'about-intro',
        'diversity',
        'cta-section',
        'faq',
        'newsletter',
    ];

    /** @var array<int, string> */
    public const BASIC_CONTENT_TYPES = [
        'hero',
        'membership',
        'about-intro',
        'cta-section',
    ];

    public static function definitions(): array
    {
        return [
            'hero' => [
                'label' => 'Hero 主视觉',
                'description' => '首屏小标题、大标题、正文与 CTA 按钮（蓝底/白底）',
                'fields' => ['tagline', 'title_lines', 'description', 'buttons'],
            ],
            'footnote-cards' => [
                'label' => '脚注卡片',
                'description' => 'Hero 下方最多 6 张快捷入口卡片（图片、标题、链接）',
                'fields' => ['items'],
            ],
            'membership' => [
                'label' => '会员推广',
                'description' => '两栏布局：小标题、大标题、正文与 CTA 按钮（可多个）',
                'fields' => ['tagline', 'title_lines', 'description', 'buttons'],
            ],
            'stats' => [
                'label' => '数据统计',
                'description' => '最多 3 项关键数字卡片（数字、标题、内容）',
                'fields' => ['items'],
            ],
            'cpd-intro' => [
                'label' => 'CPD 介绍',
                'description' => '居中标题 HTML，支持 span 渐变 class',
                'fields' => ['content'],
            ],
            'tabbed-content' => [
                'label' => '选项卡内容',
                'description' => '可切换标签：按钮名、小标题、大标题、内容、链接按钮与右侧图片',
                'fields' => ['tabs'],
            ],
            'testimonials' => [
                'label' => '会员推荐',
                'description' => '轮播推荐：标题（可换行）、内容与头像图片',
                'fields' => ['items'],
            ],
            'about-intro' => [
                'label' => '关于 IPA',
                'description' => '小标题、大标题、正文与 CTA 按钮（可多个，蓝底/白底）',
                'fields' => ['tagline', 'title_lines', 'description', 'buttons'],
            ],
            'diversity' => [
                'label' => '多元与包容',
                'description' => '上方配图（圆角）+ 下方居中标题 HTML，支持渐变 span',
                'fields' => ['image', 'title'],
            ],
            'cta-section' => [
                'label' => '塑造未来',
                'description' => '左图右文：小标题、大标题、正文、按钮与配图',
                'fields' => ['tagline', 'title_lines', 'description', 'image', 'buttons'],
            ],
            'faq' => [
                'label' => '常见问题',
                'description' => 'FAQ 手风琴：问题与答案，可添加多项',
                'fields' => ['items'],
            ],
            'newsletter' => [
                'label' => '邮件订阅',
                'description' => '左：标题与正文 HTML；右：固定订阅表单，提交按钮可自定义',
                'fields' => ['title', 'content', 'button_text'],
            ],
        ];
    }

    public static function isStructured(string $type): bool
    {
        return in_array($type, self::STRUCTURED_TYPES, true);
    }

    public static function options(): array
    {
        return collect(static::definitions())
            ->mapWithKeys(fn (array $def, string $key) => [$key => $def['label']])
            ->all();
    }

    public static function label(string $key): string
    {
        return static::definitions()[$key]['label'] ?? $key;
    }

    public static function defaultSortOrder(string $key): int
    {
        $keys = array_keys(static::definitions());

        $index = array_search($key, $keys, true);

        return $index === false ? 0 : $index;
    }

    public static function defaultData(string $key): array
    {
        return match ($key) {
            'hero' => HeroSectionData::emptyStorage(),
            'footnote-cards' => FootnoteCardsSectionData::emptyStorage(),
            'membership' => MembershipSectionData::emptyStorage(),
            'stats' => StatsSectionData::emptyStorage(),
            'cpd-intro' => CpdIntroSectionData::emptyStorage(),
            'tabbed-content' => TabbedContentSectionData::emptyStorage(),
            'testimonials' => TestimonialsSectionData::emptyStorage(),
            'about-intro' => AboutIntroSectionData::defaultStorage(),
            'diversity' => DiversitySectionData::defaultStorage(),
            'cta-section' => CtaSectionData::defaultStorage(),
            'faq' => FaqSectionData::defaultStorage(),
            'newsletter' => NewsletterSectionData::defaultStorage(),
            default => array_fill_keys(static::definitions()[$key]['fields'] ?? [], null),
        };
    }
}
