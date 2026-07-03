<?php

namespace App\Filament\Resources\PageResource\Forms;

use App\Filament\Forms\ImageUpload;
use App\Support\PageTemplate\PageBodyBlocks;
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
            Forms\Components\RichEditor::make('content')
                ->label('正文内容')
                ->columnSpanFull()
                ->toolbarButtons([
                    ['bold', 'italic', 'underline', 'strike'],
                    ['h2', 'h3', 'blockquote'],
                    ['bulletList', 'orderedList'],
                    ['link'],
                    ['undo', 'redo'],
                    ['source-ai'],
                ]),
            self::buttonsRepeater(
                'buttons',
                '可选；不添加或文字/链接留空则不显示按钮',
                requireButtonFields: false,
            ),
        ];
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
                ->helperText('与首页 FAQ 相同；至少填写问题。答案支持 HTML 链接')
                ->schema([
                    Forms\Components\TextInput::make('question')
                        ->label('问题')
                        ->required()
                        ->maxLength(500)
                        ->columnSpanFull(),
                    Forms\Components\Textarea::make('answer')
                        ->label('答案')
                        ->rows(4)
                        ->columnSpanFull(),
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

    public static function buttonsRepeater(
        string $name = 'buttons',
        ?string $helperText = null,
        bool $requireButtonFields = true,
        int $minItems = 0,
    ): Forms\Components\Repeater {
        return Forms\Components\Repeater::make($name)
            ->label('按钮')
            ->helperText($helperText)
            ->schema([
                Forms\Components\TextInput::make('label')
                    ->label('按钮文字')
                    ->required($requireButtonFields)
                    ->maxLength(120),
                Forms\Components\TextInput::make('url')
                    ->label('链接')
                    ->required($requireButtonFields)
                    ->placeholder('https:// 或 /category/...')
                    ->maxLength(2048),
                Forms\Components\Select::make('style')
                    ->label('样式')
                    ->options([
                        'primary' => '蓝底白字（主按钮）',
                        'secondary' => '白底蓝字（次按钮）',
                    ])
                    ->default('primary')
                    ->required($requireButtonFields),
                Forms\Components\Select::make('target')
                    ->label('打开方式')
                    ->options([
                        '' => '当前窗口',
                        '_blank' => '新窗口',
                    ])
                    ->default(''),
            ])
            ->minItems($minItems)
            ->maxItems(6)
            ->reorderable()
            ->addActionLabel('添加按钮')
            ->columns(2)
            ->columnSpanFull();
    }
}
