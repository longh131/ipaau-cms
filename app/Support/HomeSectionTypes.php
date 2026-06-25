<?php

namespace App\Support;

use App\Support\HomeSection\FootnoteCardsSectionData;
use App\Support\HomeSection\HeroSectionData;

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
                'description' => '会员权益与加入引导',
                'fields' => ['title', 'body', 'cta_text', 'cta_url'],
            ],
            'stats' => [
                'label' => '数据统计',
                'description' => '关键数字展示',
                'fields' => ['items'],
            ],
            'cpd-intro' => [
                'label' => 'CPD 介绍',
                'description' => '持续专业发展板块',
                'fields' => ['title', 'body', 'cta_text', 'cta_url'],
            ],
            'tabbed-content' => [
                'label' => '选项卡内容',
                'description' => 'Events / Courses / Online CPD 等切换区',
                'fields' => ['tabs'],
            ],
            'testimonials' => [
                'label' => '用户评价',
                'description' => '会员 testimonial 轮播',
                'fields' => ['items'],
            ],
            'about-intro' => [
                'label' => '关于 IPA',
                'description' => '机构简介与价值主张',
                'fields' => ['title', 'body', 'cta_text', 'cta_url'],
            ],
            'diversity' => [
                'label' => '多元与包容',
                'description' => 'DEI 相关宣传内容',
                'fields' => ['title', 'body'],
            ],
            'cta-section' => [
                'label' => '行动号召',
                'description' => '中部 CTA 横幅',
                'fields' => ['title', 'body', 'cta_text', 'cta_url', 'image'],
            ],
            'faq' => [
                'label' => '常见问题',
                'description' => 'FAQ 手风琴列表',
                'fields' => ['items'],
            ],
            'newsletter' => [
                'label' => '邮件订阅',
                'description' => 'Newsletter 注册表单文案',
                'fields' => ['title', 'subtitle', 'button_text'],
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
            default => array_fill_keys(static::definitions()[$key]['fields'] ?? [], null),
        };
    }
}
