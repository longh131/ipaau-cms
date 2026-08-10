<?php

namespace App\Support\CategoryListTemplate;

/**
 * 「会员风采」栏目扩展字段（在后台栏目 → 文章扩展字段 中配置）：
 *
 * | 字段键名   | 字段标签 | 字段类型   | 列表页显示 |
 * | position  | 职务     | 多行文本   | 是         |
 *
 * 封面图使用文章标准字段「封面图片」；姓名/标题使用文章「标题」；摘要使用文章「摘要」；正文使用文章「内容」。
 */
final class MemberSpotlightTemplate
{
    public const POSITION_KEY = 'position';

    public const PER_PAGE = 100;

    /** @var array{key: string, label: string, type: string, show_in_list: bool} */
    public const RECOMMENDED_EXTRA_FIELD = [
        'key' => self::POSITION_KEY,
        'label' => '职务',
        'type' => 'textarea',
        'show_in_list' => true,
    ];
}
