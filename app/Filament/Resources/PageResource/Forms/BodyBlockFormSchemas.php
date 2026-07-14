<?php

namespace App\Filament\Resources\PageResource\Forms;

use App\Filament\Forms\ImageUpload;
use App\Support\PageTemplate\GeneralSecondarySections;
use App\Support\PageTemplate\PageBodyBlocks;
use App\Support\RichContent;
use Filament\Forms;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;

class BodyBlockFormSchemas
{
    /**
     * @return array<int, Forms\Components\Component>
     */
    public static function tabsRepeaterFields(): array
    {
        return [
            Forms\Components\Repeater::make('tabs')
                ->label('选项卡列表')
                ->helperText('与首页「选项卡内容」相同：左侧文案与按钮，右侧切换图片')
                ->schema([
                    Forms\Components\TextInput::make('tab_label')
                        ->label('切换按钮名称')
                        ->required()
                        ->maxLength(80)
                        ->columnSpanFull(),
                    Forms\Components\TextInput::make('tagline')
                        ->label('小标题')
                        ->maxLength(255)
                        ->columnSpanFull(),
                    Forms\Components\TextInput::make('title')
                        ->label('大标题')
                        ->maxLength(255)
                        ->columnSpanFull(),
                    Forms\Components\Textarea::make('description')
                        ->label('内容')
                        ->rows(4)
                        ->helperText('空行可分段显示')
                        ->columnSpanFull(),
                    Forms\Components\TextInput::make('button_label')
                        ->label('链接按钮名称')
                        ->maxLength(120),
                    Forms\Components\TextInput::make('button_url')
                        ->label('链接按钮地址')
                        ->placeholder('/category/ 或 https://')
                        ->maxLength(2048),
                    ImageUpload::make(
                        'image',
                        'page-components/pages/tabs',
                        '右侧图片',
                        '切换到此卡时在右侧显示',
                    )->columnSpanFull(),
                ])
                ->minItems(1)
                ->maxItems(8)
                ->reorderable()
                ->addActionLabel('添加选项卡')
                ->columns(2)
                ->columnSpanFull(),
        ];
    }

    /**
     * @return array<int, Forms\Components\Component>
     */
    public static function carouselFields(): array
    {
        return [
            Forms\Components\TextInput::make('heading')
                ->label('板块标题')
                ->placeholder('例如：Hear from our Members')
                ->maxLength(255)
                ->columnSpanFull(),
            Forms\Components\Repeater::make('slides')
                ->label('轮播条目')
                ->helperText('参考 About 页会员推荐轮播；至少填写引用或署名之一')
                ->schema([
                    Forms\Components\Textarea::make('quote')
                        ->label('引用内容')
                        ->rows(5)
                        ->columnSpanFull(),
                    Forms\Components\TextInput::make('author')
                        ->label('署名')
                        ->maxLength(120)
                        ->columnSpanFull(),
                ])
                ->minItems(1)
                ->maxItems(12)
                ->reorderable()
                ->addActionLabel('添加条目')
                ->columnSpanFull(),
        ];
    }

    /**
     * @return array<int, Forms\Components\Component>
     */
    public static function mediaSplitFields(): array
    {
        return [
            Forms\Components\Select::make('image_position')
                ->label('图片位置')
                ->options(PageBodyBlocks::IMAGE_POSITION_OPTIONS)
                ->default('left')
                ->required()
                ->columnSpan(1),
            Forms\Components\Select::make('image_shape')
                ->label('图片形状')
                ->options(PageBodyBlocks::IMAGE_SHAPE_OPTIONS)
                ->default('acorn')
                ->required()
                ->columnSpan(1),
            ImageUpload::make(
                'image',
                'page-components/pages/media-split',
                '配图',
                '建议竖向或正方形；与 About 页图文分栏一致',
            )->columnSpanFull(),
            Forms\Components\TextInput::make('tagline')
                ->label('小标题')
                ->maxLength(255)
                ->columnSpanFull(),
            Forms\Components\TextInput::make('title')
                ->label('大标题')
                ->maxLength(255)
                ->columnSpanFull(),
            RichContent::nestedRichEditor('content', '正文内容'),
        ];
    }

    /**
     * @return array<int, Forms\Components\Component>
     */
    public static function contentColumnsFields(): array
    {
        return [
            Fieldset::make('左栏')
                ->statePath('left_column')
                ->schema(self::contentColumnSchema())
                ->columnSpanFull(),
            Fieldset::make('右栏')
                ->statePath('right_column')
                ->schema(self::contentColumnSchema())
                ->columnSpanFull(),
        ];
    }

    /**
     * @return array<int, Forms\Components\Component>
     */
    public static function contentColumnSchema(): array
    {
        return [
            Forms\Components\TextInput::make('title')
                ->label('标题')
                ->maxLength(255)
                ->columnSpanFull(),
            RichContent::nestedRichEditor('content', '内容'),
            Forms\Components\TextInput::make('button_label')
                ->label('按钮文字')
                ->maxLength(120)
                ->placeholder('例如：Download')
                ->helperText('需同时填写按钮文字与链接，保存后前台才会显示按钮')
                ->columnSpan(1),
            Forms\Components\TextInput::make('button_url')
                ->label('按钮链接')
                ->placeholder('https:// 或 /category/...')
                ->maxLength(2048)
                ->columnSpan(1),
            Forms\Components\Select::make('button_style')
                ->label('按钮样式')
                ->options([
                    'primary' => '蓝底白字（主按钮）',
                    'secondary' => '白底蓝字（次按钮）',
                ])
                ->default('primary')
                ->columnSpan(1),
            Forms\Components\Select::make('button_target')
                ->label('打开方式')
                ->options([
                    '' => '当前窗口',
                    '_blank' => '新窗口',
                ])
                ->default('')
                ->columnSpan(1),
        ];
    }

    /**
     * @return array<int, Forms\Components\Component>
     */
    public static function generalSecondaryContentBlockFields(): array
    {
        return [
            Forms\Components\TextInput::make('tagline')
                ->label('小标题')
                ->placeholder('例如：ABOUT THE IPA')
                ->helperText('样式与首页 Hero 主视觉小标题一致')
                ->maxLength(255)
                ->columnSpanFull(),
            Forms\Components\TextInput::make('title')
                ->label('标题')
                ->placeholder('例如：Strategic Plan')
                ->maxLength(255)
                ->columnSpanFull(),
            Forms\Components\Select::make('title_align')
                ->label('标题对齐')
                ->options(PageBodyBlocks::TITLE_ALIGN_OPTIONS)
                ->default('left')
                ->columnSpanFull(),
            RichContent::nestedRichEditor('content', '内容'),
            self::sectionButtonsRepeater(),
        ];
    }

    /**
     * @return array<int, Forms\Components\Component>
     */
    public static function generalSecondaryLeftRightLayoutFields(): array
    {
        return [
            Forms\Components\TextInput::make('title')
                ->label('大标题')
                ->placeholder('例如：Professional Indemnity Insurance')
                ->helperText('显示在左侧顶部，前台以渐变样式呈现')
                ->maxLength(255)
                ->columnSpanFull(),
            Forms\Components\Select::make('title_gradient')
                ->label('大标题渐变')
                ->options(PageBodyBlocks::GRADIENT_OPTIONS)
                ->default('purple-reverse')
                ->columnSpanFull(),
            Forms\Components\TextInput::make('tagline')
                ->label('小标题')
                ->placeholder('例如：Insurance Requirements')
                ->helperText('显示在大标题下方，颜色 #992785，字号与大标题相同、非粗体')
                ->maxLength(255)
                ->columnSpanFull(),
            RichContent::nestedRichEditor('content', '右侧内容'),
            self::sectionButtonsRepeater(),
        ];
    }

    /**
     * @return array<int, Forms\Components\Component>
     */
    public static function generalSecondaryTabbedContentFields(): array
    {
        return [
            Forms\Components\Repeater::make('tabs')
                ->label('选项卡列表')
                ->helperText('与首页「选项卡内容」类似，但无右侧图片；内容为富文本，前台占满整行宽度。至少填写「切换按钮名称」才会显示该卡。')
                ->schema([
                    Forms\Components\TextInput::make('tab_label')
                        ->label('切换按钮名称')
                        ->required()
                        ->maxLength(80)
                        ->columnSpanFull(),
                    Forms\Components\TextInput::make('tagline')
                        ->label('小标题')
                        ->maxLength(255)
                        ->columnSpanFull(),
                    Forms\Components\TextInput::make('title')
                        ->label('大标题')
                        ->maxLength(255)
                        ->columnSpanFull(),
                    RichContent::nestedRichEditor('content', '内容'),
                    Forms\Components\TextInput::make('button_label')
                        ->label('链接按钮名称')
                        ->maxLength(120),
                    Forms\Components\TextInput::make('button_url')
                        ->label('链接按钮地址')
                        ->placeholder('/articles/ 或 https://')
                        ->maxLength(2048),
                ])
                ->minItems(1)
                ->maxItems(8)
                ->reorderable()
                ->addActionLabel('添加选项卡')
                ->columns(2)
                ->columnSpanFull(),
        ];
    }

    /**
     * @return array<int, Forms\Components\Component>
     */
    public static function generalSecondaryMediaSplitFields(): array
    {
        return [
            ...self::mediaSplitFields(),
            self::sectionButtonsRepeater(),
        ];
    }

    public static function sectionButtonsRepeater(): Forms\Components\Repeater
    {
        return Forms\Components\Repeater::make('buttons')
            ->label('按钮')
            ->helperText('可选；需同时填写按钮文字与链接，保存后前台才会显示')
            ->schema([
                Forms\Components\TextInput::make('label')
                    ->label('按钮文字')
                    ->maxLength(120),
                Forms\Components\TextInput::make('url')
                    ->label('链接')
                    ->placeholder('https:// 或 /category/...')
                    ->maxLength(2048),
                Forms\Components\Select::make('style')
                    ->label('样式')
                    ->options([
                        'primary' => '蓝底白字（主按钮）',
                        'secondary' => '白底蓝字（次按钮）',
                    ])
                    ->default('primary'),
                Forms\Components\Select::make('target')
                    ->label('打开方式')
                    ->options([
                        '' => '当前窗口',
                        '_blank' => '新窗口',
                    ])
                    ->default(''),
            ])
            ->minItems(0)
            ->maxItems(6)
            ->reorderable()
            ->addActionLabel('添加按钮')
            ->columns(2)
            ->columnSpanFull();
    }

    /**
     * @return array<int, Forms\Components\Component>
     */
    public static function faqFields(): array
    {
        return [
            Forms\Components\TextInput::make('tagline')
                ->label('小标题')
                ->placeholder('例如：HAVE A QUESTION?')
                ->maxLength(255)
                ->columnSpanFull(),
            Forms\Components\TextInput::make('title')
                ->label('板块标题')
                ->placeholder('例如：Frequently Asked Questions')
                ->maxLength(255)
                ->columnSpanFull(),
            Forms\Components\Textarea::make('intro')
                ->label('导语')
                ->rows(2)
                ->placeholder('显示在标题下方，可选')
                ->columnSpanFull(),
            Forms\Components\Repeater::make('items')
                ->label('问答项')
                ->helperText('至少填写问题；答案支持段落、列表与链接')
                ->schema([
                    Forms\Components\TextInput::make('question')
                        ->label('问题')
                        ->required()
                        ->maxLength(500)
                        ->columnSpanFull(),
                    RichContent::nestedRichEditor('answer', '答案', self::faqAnswerToolbar()),
                ])
                ->minItems(1)
                ->maxItems(30)
                ->reorderable()
                ->addActionLabel('添加问答')
                ->columnSpanFull(),
        ];
    }

    /**
     * @return array<int, Forms\Components\Component>
     */
    public static function statsFields(): array
    {
        return [
            Forms\Components\Repeater::make('items')
                ->label('统计项')
                ->helperText('与首页「数据统计」相同；最多 4 项，数字可填文字或上传图片')
                ->schema([
                    Forms\Components\Radio::make('number_type')
                        ->label('数字展示方式')
                        ->options([
                            'text' => '文字',
                            'image' => '图片',
                        ])
                        ->default('text')
                        ->live()
                        ->afterStateUpdated(function (Set $set, ?string $state): void {
                            if ($state === 'text') {
                                $set('number_image', null);
                            } else {
                                $set('number', null);
                            }
                        })
                        ->columnSpanFull(),
                    Forms\Components\TextInput::make('number')
                        ->label('数字')
                        ->placeholder('例如：60k+、13、6+')
                        ->maxLength(30)
                        ->visible(fn (Get $get): bool => ($get('number_type') ?? 'text') === 'text')
                        ->required(fn (Get $get): bool => ($get('number_type') ?? 'text') === 'text')
                        ->columnSpanFull(),
                    ImageUpload::make(
                        'number_image',
                        'page-components/pages/stats',
                        '数字图片',
                        '用于替代文字数字，建议 PNG/SVG 透明底',
                    )
                        ->visible(fn (Get $get): bool => ($get('number_type') ?? 'text') === 'image')
                        ->required(fn (Get $get): bool => ($get('number_type') ?? 'text') === 'image')
                        ->columnSpanFull(),
                    Forms\Components\TextInput::make('title')
                        ->label('标题')
                        ->maxLength(255)
                        ->columnSpanFull(),
                    Forms\Components\Textarea::make('content')
                        ->label('内容')
                        ->rows(3)
                        ->columnSpanFull(),
                ])
                ->minItems(1)
                ->maxItems(4)
                ->reorderable()
                ->addActionLabel('添加统计项')
                ->columnSpanFull(),
        ];
    }

    /**
     * @return array<int, Forms\Components\Component>
     */
    public static function testimonialsFields(): array
    {
        return [
            Forms\Components\TextInput::make('section_title')
                ->label('大标题')
                ->placeholder('例如：Hear from our Members')
                ->helperText('显示在轮播上方')
                ->maxLength(255)
                ->columnSpanFull(),
            Forms\Components\Repeater::make('items')
                ->label('推荐列表')
                ->helperText('与首页「会员推荐」相同；轮播展示，标题支持换行')
                ->schema([
                    Forms\Components\Textarea::make('title')
                        ->label('标题')
                        ->rows(3)
                        ->helperText('可换行，每行单独显示')
                        ->columnSpanFull(),
                    Forms\Components\Textarea::make('content')
                        ->label('内容')
                        ->rows(5)
                        ->columnSpanFull(),
                    ImageUpload::make(
                        'image',
                        'page-components/testimonials',
                        '图片',
                        '建议正方形头像（如 400×400）',
                    )->columnSpanFull(),
                ])
                ->minItems(1)
                ->maxItems(12)
                ->reorderable()
                ->addActionLabel('添加推荐')
                ->columnSpanFull(),
        ];
    }

    /**
     * @return array<int, Forms\Components\Component>
     */
    public static function newsletterFields(): array
    {
        return [
            Forms\Components\TextInput::make('title')
                ->label('标题')
                ->placeholder('例如：Subscribe to our newsletter')
                ->maxLength(255)
                ->columnSpanFull(),
            RichContent::nestedRichEditor(
                'content',
                '正文',
                RichContent::pageToolbar(),
                '与首页「邮件订阅」相同；左侧说明文字，右侧表单字段固定',
            ),
            Forms\Components\TextInput::make('button_text')
                ->label('提交按钮文字')
                ->default('提交')
                ->maxLength(60)
                ->columnSpanFull(),
        ];
    }

    /**
     * @return array<int, Forms\Components\Component>
     */
    public static function htmlBodyFields(): array
    {
        return [
            Forms\Components\Textarea::make('body')
                ->label('正文（HTML 源码）')
                ->rows(24)
                ->helperText('直接粘贴或编写 HTML，保存后前台原样渲染；与基本正文页相同。')
                ->columnSpanFull()
                ->extraInputAttributes([
                    'class' => 'font-mono text-sm',
                    'spellcheck' => 'false',
                ]),
        ];
    }

    public static function newsListFields(): array
    {
        return [
            Forms\Components\TextInput::make('section_title')
                ->label('板块标题')
                ->placeholder('例如：Latest News')
                ->maxLength(255)
                ->columnSpanFull(),
            Forms\Components\Select::make('section_background')
                ->label('板块背景')
                ->options(GeneralSecondarySections::NEWS_LIST_BACKGROUND_OPTIONS)
                ->default(GeneralSecondarySections::NEWS_LIST_BG_GRAY)
                ->required()
                ->columnSpanFull(),
            RichContent::nestedRichEditor('summary', '摘要'),
            Forms\Components\TextInput::make('view_more_label')
                ->label('「查看更多」按钮文字')
                ->default('查看更多')
                ->maxLength(80)
                ->columnSpanFull(),
            Forms\Components\Repeater::make('items')
                ->label('新闻列表')
                ->helperText('前台默认显示 3 条，其余通过「查看更多」展开；每行 3 个卡片')
                ->schema([
                    Forms\Components\TextInput::make('title')
                        ->label('新闻标题')
                        ->required()
                        ->maxLength(255)
                        ->columnSpanFull(),
                    Forms\Components\Textarea::make('summary')
                        ->label('概要')
                        ->rows(3)
                        ->maxLength(1000)
                        ->columnSpanFull(),
                    Forms\Components\TextInput::make('url')
                        ->label('链接')
                        ->placeholder('https:// 或 /category/...')
                        ->maxLength(2048)
                        ->columnSpan(1),
                    Forms\Components\Select::make('target')
                        ->label('打开方式')
                        ->options([
                            '' => '当前窗口',
                            '_blank' => '新窗口',
                        ])
                        ->default('_blank')
                        ->columnSpan(1),
                ])
                ->minItems(1)
                ->maxItems(48)
                ->reorderable()
                ->addActionLabel('添加新闻')
                ->columns(2)
                ->columnSpanFull(),
        ];
    }

    public static function generalSecondaryNewsListFields(): array
    {
        return [
            Forms\Components\TextInput::make('section_title')
                ->label('板块标题')
                ->placeholder('例如：Member Benefits')
                ->maxLength(255)
                ->columnSpanFull(),
            Forms\Components\Select::make('section_background')
                ->label('板块背景')
                ->options(GeneralSecondarySections::NEWS_LIST_BACKGROUND_OPTIONS)
                ->default(GeneralSecondarySections::NEWS_LIST_BG_GRAY)
                ->required()
                ->columnSpanFull(),
            RichContent::nestedRichEditor('summary', '摘要'),
            Forms\Components\TextInput::make('view_more_label')
                ->label('「查看更多」按钮文字')
                ->default('查看更多')
                ->maxLength(80)
                ->columnSpanFull(),
            Forms\Components\Repeater::make('items')
                ->label('列表条目')
                ->helperText('前台默认显示 4 条（2 列 × 2 行），其余通过「查看更多」展开；样式参考会员权益页 cardListCurated')
                ->schema([
                    ImageUpload::make(
                        'icon',
                        'page-components/pages/news-list-icons',
                        '图标',
                        '建议正方形 PNG/SVG，显示在标题上方',
                    )->columnSpanFull(),
                    Forms\Components\TextInput::make('title')
                        ->label('标题')
                        ->required()
                        ->maxLength(255)
                        ->columnSpanFull(),
                    Forms\Components\Textarea::make('summary')
                        ->label('概要')
                        ->rows(3)
                        ->maxLength(1000)
                        ->columnSpanFull(),
                    Forms\Components\TextInput::make('link_title')
                        ->label('链接标题')
                        ->placeholder('例如：Learn more')
                        ->helperText('显示在卡片底部链接文字；需同时填写链接地址')
                        ->maxLength(120)
                        ->columnSpanFull(),
                    Forms\Components\TextInput::make('url')
                        ->label('链接')
                        ->placeholder('https:// 或 /category/...')
                        ->maxLength(2048)
                        ->columnSpan(1),
                    Forms\Components\Select::make('target')
                        ->label('打开方式')
                        ->options([
                            '' => '当前窗口',
                            '_blank' => '新窗口',
                        ])
                        ->default('_blank')
                        ->columnSpan(1),
                ])
                ->minItems(1)
                ->maxItems(48)
                ->reorderable()
                ->addActionLabel('添加条目')
                ->columns(2)
                ->columnSpanFull(),
        ];
    }

    /**
     * @return array<int, Forms\Components\Component>
     */
    public static function cardListCuratedFields(): array
    {
        return [
            Forms\Components\TextInput::make('section_title')
                ->label('板块标题')
                ->placeholder('例如：Annual Reports')
                ->maxLength(255)
                ->columnSpanFull(),
            Forms\Components\Repeater::make('items')
                ->label('卡片')
                ->schema([
                    Forms\Components\TextInput::make('title')
                        ->label('标题')
                        ->required()
                        ->maxLength(255)
                        ->columnSpanFull(),
                    Forms\Components\TextInput::make('url')
                        ->label('链接')
                        ->placeholder('https:// 或 /category/...')
                        ->maxLength(2048)
                        ->columnSpan(1),
                    Forms\Components\Select::make('target')
                        ->label('打开方式')
                        ->options([
                            '' => '当前窗口',
                            '_blank' => '新窗口',
                        ])
                        ->default('_blank')
                        ->columnSpan(1),
                ])
                ->minItems(0)
                ->maxItems(24)
                ->reorderable()
                ->addActionLabel('添加卡片')
                ->columns(2)
                ->columnSpanFull(),
        ];
    }

    public static function bodyBlockButtonsRepeater(): Forms\Components\Repeater
    {
        return Forms\Components\Repeater::make('buttons')
            ->label('按钮')
            ->helperText(fn (Get $get): string => match ($get('type')) {
                PageBodyBlocks::TYPE_CTA_GROUP => '保存后，这一组按钮会作为独立一行显示在前后区块之间',
                PageBodyBlocks::TYPE_MEDIA_SPLIT => '可选；需同时填写按钮文字与链接，保存后前台才会显示',
                default => '',
            })
            ->visible(fn (Get $get): bool => in_array($get('type'), [
                PageBodyBlocks::TYPE_CTA_GROUP,
                PageBodyBlocks::TYPE_MEDIA_SPLIT,
            ], true))
            ->schema([
                Forms\Components\TextInput::make('label')
                    ->label('按钮文字')
                    ->required(fn (Get $get): bool => $get('../../type') === PageBodyBlocks::TYPE_CTA_GROUP)
                    ->maxLength(120),
                Forms\Components\TextInput::make('url')
                    ->label('链接')
                    ->required(fn (Get $get): bool => $get('../../type') === PageBodyBlocks::TYPE_CTA_GROUP)
                    ->placeholder('https:// 或 /category/...')
                    ->maxLength(2048),
                Forms\Components\Select::make('style')
                    ->label('样式')
                    ->options([
                        'primary' => '蓝底白字（主按钮）',
                        'secondary' => '白底蓝字（次按钮）',
                    ])
                    ->default('primary')
                    ->required(fn (Get $get): bool => $get('../../type') === PageBodyBlocks::TYPE_CTA_GROUP),
                Forms\Components\Select::make('target')
                    ->label('打开方式')
                    ->options([
                        '' => '当前窗口',
                        '_blank' => '新窗口',
                    ])
                    ->default(''),
            ])
            ->minItems(fn (Get $get): int => $get('type') === PageBodyBlocks::TYPE_CTA_GROUP ? 1 : 0)
            ->maxItems(6)
            ->reorderable()
            ->addActionLabel('添加按钮')
            ->columns(2)
            ->columnSpanFull();
    }

    /**
     * @return array<int, array<int, string>>
     */
    public static function faqAnswerToolbar(): array
    {
        return [
            ['bold', 'italic', 'underline'],
            ['h4'],
            ['alignStart', 'alignCenter', 'alignEnd'],
            ['bulletList', 'orderedList'],
            ['link'],
            ['undo', 'redo'],
        ];
    }

    /**
     * 默认正文页：按区块类型只挂载对应字段，避免 Repeater 项携带全部类型的空状态。
     *
     * @return array<int, Forms\Components\Component>
     */
    public static function defaultPageBlockFields(?string $type): array
    {
        return match ($type) {
            PageBodyBlocks::TYPE_RICH_TEXT => [
                Forms\Components\TextInput::make('title')
                    ->label('标题')
                    ->placeholder('例如：Who We Are')
                    ->helperText('可选；显示在正文上方，样式与 About 页章节标题一致')
                    ->maxLength(255)
                    ->columnSpanFull(),
                Forms\Components\Select::make('title_align')
                    ->label('标题对齐')
                    ->options(PageBodyBlocks::TITLE_ALIGN_OPTIONS)
                    ->default('center')
                    ->columnSpanFull(),
                RichContent::nestedRichEditor('html', '段落内容'),
            ],
            PageBodyBlocks::TYPE_HIGHLIGHT => [
                Forms\Components\Textarea::make('text')
                    ->label('强调文字')
                    ->rows(3)
                    ->helperText('整句或关键词，前台以渐变样式显示')
                    ->columnSpanFull(),
                Forms\Components\Select::make('gradient')
                    ->label('渐变样式')
                    ->options(PageBodyBlocks::GRADIENT_OPTIONS)
                    ->default('purple-reverse')
                    ->columnSpanFull(),
            ],
            PageBodyBlocks::TYPE_CTA_GROUP => [
                self::bodyBlockButtonsRepeater()
                    ->visible(true)
                    ->helperText('保存后，这一组按钮会作为独立一行显示在前后区块之间'),
            ],
            PageBodyBlocks::TYPE_TABS => self::tabsRepeaterFields(),
            PageBodyBlocks::TYPE_CAROUSEL => self::carouselFields(),
            PageBodyBlocks::TYPE_MEDIA_SPLIT => [
                ...self::mediaSplitFields(),
                self::bodyBlockButtonsRepeater()
                    ->visible(true)
                    ->helperText('可选；需同时填写按钮文字与链接，保存后前台才会显示'),
            ],
            PageBodyBlocks::TYPE_CONTENT_COLUMNS => self::contentColumnsFields(),
            PageBodyBlocks::TYPE_FAQ => self::faqFields(),
            PageBodyBlocks::TYPE_STATS => self::statsFields(),
            PageBodyBlocks::TYPE_CARD_LIST_CURATED => self::cardListCuratedFields(),
            PageBodyBlocks::TYPE_NEWS_LIST => self::newsListFields(),
            PageBodyBlocks::TYPE_HTML_BODY => self::htmlBodyFields(),
            default => [],
        };
    }

    /**
     * 通用二级页：按板块类型只挂载对应字段，避免 Repeater 内多个 `content` 富文本共用 schema key。
     *
     * @return array<int, Forms\Components\Component>
     */
    public static function generalSecondaryBlockFields(?string $type): array
    {
        return match ($type) {
            GeneralSecondarySections::TYPE_CONTENT_BLOCK => self::generalSecondaryContentBlockFields(),
            GeneralSecondarySections::TYPE_FAQ => self::faqFields(),
            GeneralSecondarySections::TYPE_NEWS_LIST_A => self::newsListFields(),
            GeneralSecondarySections::TYPE_NEWS_LIST => self::generalSecondaryNewsListFields(),
            GeneralSecondarySections::TYPE_STATS => self::statsFields(),
            GeneralSecondarySections::TYPE_TESTIMONIALS => self::testimonialsFields(),
            GeneralSecondarySections::TYPE_NEWSLETTER => self::newsletterFields(),
            GeneralSecondarySections::TYPE_HTML_BODY => self::htmlBodyFields(),
            GeneralSecondarySections::TYPE_LEFT_RIGHT_LAYOUT => self::generalSecondaryLeftRightLayoutFields(),
            GeneralSecondarySections::TYPE_TABBED_CONTENT => self::generalSecondaryTabbedContentFields(),
            GeneralSecondarySections::TYPE_MEDIA_SPLIT => self::generalSecondaryMediaSplitFields(),
            default => [],
        };
    }
}
