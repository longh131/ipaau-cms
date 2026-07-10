<?php

namespace App\Filament\Resources\PageResource\Forms;

use App\Filament\Forms\ImageUpload;
use App\Support\PageTemplate\PageBodyBlocks;
use App\Support\RichContent;
use Filament\Forms;
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
            RichContent::configureFileAttachments(
                Forms\Components\RichEditor::make('content')
                    ->label('正文内容')
                    ->columnSpanFull()
                    ->toolbarButtons(RichContent::pageToolbar())
                    ->helperText(RichContent::imageUploadHelperText()),
            ),
        ];
    }

    /**
     * @return array<int, Forms\Components\Component>
     */
    public static function contentColumnsFields(): array
    {
        return [
            Forms\Components\Repeater::make('columns')
                ->label('左右分栏')
                ->helperText('固定左栏、右栏两列；每栏含标题、富文本与可选按钮')
                ->schema(self::contentColumnSchema())
                ->defaultItems(2)
                ->minItems(2)
                ->maxItems(2)
                ->reorderable(false)
                ->addable(false)
                ->deletable(false)
                ->itemLabel(fn (array $state, string $uuid, ?int $index = null): string => $index === 1 ? '右栏' : '左栏')
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
            RichContent::configureFileAttachments(
                Forms\Components\RichEditor::make('content')
                    ->label('内容')
                    ->columnSpanFull()
                    ->toolbarButtons(RichContent::pageToolbar())
                    ->helperText(RichContent::imageUploadHelperText()),
            ),
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
            RichContent::configureFileAttachments(
                Forms\Components\RichEditor::make('content')
                    ->label('内容')
                    ->columnSpanFull()
                    ->toolbarButtons(RichContent::pageToolbar())
                    ->helperText(RichContent::imageUploadHelperText()),
            ),
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
                    Forms\Components\RichEditor::make('answer')
                        ->label('答案')
                        ->columnSpanFull()
                        ->toolbarButtons(self::faqAnswerToolbar()),
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

    public static function newsListFields(): array
    {
        return [
            Forms\Components\TextInput::make('section_title')
                ->label('板块标题')
                ->placeholder('例如：Latest News')
                ->maxLength(255)
                ->columnSpanFull(),
            RichContent::configureFileAttachments(
                Forms\Components\RichEditor::make('summary')
                    ->label('摘要')
                    ->columnSpanFull()
                    ->toolbarButtons(RichContent::pageToolbar())
                    ->helperText(RichContent::imageUploadHelperText()),
            ),
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
            ['alignStart', 'alignCenter', 'alignEnd'],
            ['bulletList', 'orderedList'],
            ['link'],
            ['undo', 'redo'],
        ];
    }
}
