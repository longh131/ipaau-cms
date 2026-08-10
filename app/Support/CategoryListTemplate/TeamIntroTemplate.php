<?php

namespace App\Support\CategoryListTemplate;

/**
 * 「团队介绍」栏目扩展字段（在后台栏目 → 文章扩展字段 中配置）：
 *
 * | 字段键名       | 字段标签 | 字段类型   | 列表页显示 |
 * | job_title     | 职位     | 单行文本   | 是         |
 * | english_name  | 英文名   | 单行文本   | 是         |
 *
 * 封面图使用文章标准字段「封面图片」；中文姓名使用文章「标题」；正文使用文章「内容」。
 */
final class TeamIntroTemplate
{
    public const JOB_TITLE_KEY = 'job_title';

    public const ENGLISH_NAME_KEY = 'english_name';

    /** @var array<int, array{key: string, label: string, type: string, show_in_list: bool}> */
    public const RECOMMENDED_EXTRA_FIELDS = [
        [
            'key' => self::JOB_TITLE_KEY,
            'label' => '职位',
            'type' => 'text',
            'show_in_list' => true,
        ],
        [
            'key' => self::ENGLISH_NAME_KEY,
            'label' => '英文名',
            'type' => 'text',
            'show_in_list' => true,
        ],
    ];

    /** @var array{key: string, label: string, type: string, show_in_list: bool} */
    public const RECOMMENDED_EXTRA_FIELD = [
        'key' => self::JOB_TITLE_KEY,
        'label' => '职位',
        'type' => 'text',
        'show_in_list' => true,
    ];
}
