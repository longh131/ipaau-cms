<?php

namespace App\Filament\Resources\ArticleResource\Schemas;

use App\Models\Category;
use App\Models\Article;
use App\Filament\Forms\ImageUpload;
use App\Support\ArticleExtraFields;
use App\Support\ArticleSlug;
use App\Support\CategoryListTemplate\CategoryListTemplateRegistry;
use App\Support\RichContent;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class ArticleFormSchema
{
    public static function configure(Schema $schema, ?int $fixedCategoryId = null): Schema
    {
        return $schema->components([
            Forms\Components\TextInput::make('title')
                ->label('标题')
                ->required()
                ->live(onBlur: true)
                ->afterStateUpdated(function (?string $state, Set $set, Get $get): void {
                    if (filled(trim((string) $get('slug'))) || blank($state)) {
                        return;
                    }

                    $categoryId = (int) $get('category_id');

                    $set('slug', ArticleSlug::fromTitle(
                        $state,
                        null,
                        $categoryId > 0 ? $categoryId : null,
                    ));
                }),
            Forms\Components\TextInput::make('slug')
                ->label('别名')
                ->required()
                ->unique(ignoreRecord: true)
                ->helperText('填写标题后，点击此框或右侧按钮可自动生成；格式类似 article-栏目ID-标题片段-内容哈希，也可手动修改')
                ->suffixAction(
                    Action::make('generateSlugFromTitle')
                        ->label('根据标题生成')
                        ->icon(Heroicon::ArrowPath)
                        ->action(function (Get $get, Set $set): void {
                            if (blank($get('title'))) {
                                Notification::make()
                                    ->warning()
                                    ->title('请先填写标题')
                                    ->send();

                                return;
                            }

                            $categoryId = (int) $get('category_id');

                            $set('slug', ArticleSlug::fromTitle(
                                $get('title'),
                                null,
                                $categoryId > 0 ? $categoryId : null,
                            ));
                        }),
                )
                ->extraInputAttributes([
                    'x-on:focus' => 'if (! $el.value.trim()) { $wire.generateArticleSlugFromTitle() }',
                ]),
            Forms\Components\Select::make('category_id')
                ->label('所属栏目')
                ->options(fn (): array => Category::query()
                    ->assignableForArticles()
                    ->orderBy('sort_order')
                    ->pluck('name', 'id')
                    ->all())
                ->searchable()
                ->required()
                ->default($fixedCategoryId)
                ->disabled($fixedCategoryId !== null)
                ->dehydrated(true)
                ->live(),
            self::extraFieldsSection($fixedCategoryId),
            Forms\Components\TextInput::make('redirect_url')
                ->label('跳转链接')
                ->placeholder('如果填写了链接，点击文章时将跳转到外部链接')
                ->url(),
            Forms\Components\Textarea::make('summary')
                ->label('文章摘要')
                ->rows(3)
                ->columnSpanFull(),
            Forms\Components\TextInput::make('author')
                ->label('作者')
                ->maxLength(255)
                ->columnSpan(1),
            Forms\Components\TextInput::make('source')
                ->label('来源')
                ->maxLength(255)
                ->placeholder('例如：IPA 官网、转载媒体名称')
                ->columnSpan(1),
            Forms\Components\TextInput::make('view_count')
                ->label('访问量')
                ->numeric()
                ->minValue(0)
                ->default(0)
                ->helperText('可手动填写；用户访问文章详情页时会自动 +1')
                ->columnSpan(1),
            ImageUpload::make(
                'cover_image',
                'articles/covers',
                '封页',
            )
                ->helperText(fn (Get $get): string => CategoryListTemplateRegistry::isVideoListCategoryId((int) ($fixedCategoryId ?? $get('category_id')))
                    ? '选填，用于列表卡片封面；视频栏目也可仅填「视频文件名」，封面可留空'
                    : '建议横图，用于列表卡片封面；支持 JPG / PNG / WebP')
                ->columnSpanFull(),
            RichContent::configureFileAttachments(
                Forms\Components\RichEditor::make('content')
                    ->label('内容')
                    ->required(fn (Get $get): bool => ! CategoryListTemplateRegistry::isVideoListCategoryId((int) ($fixedCategoryId ?? $get('category_id'))))
                    ->hidden(fn (Get $get): bool => CategoryListTemplateRegistry::isVideoListCategoryId((int) ($fixedCategoryId ?? $get('category_id'))))
                    ->columnSpanFull()
                    ->toolbarButtons(RichContent::pageToolbar())
                    ->helperText(RichContent::imageUploadHelperText()),
            ),
            Forms\Components\DateTimePicker::make('published_at')
                ->label('发布时间')
                ->default(fn (): \Illuminate\Support\Carbon => now())
                ->columnSpan(1),
            Forms\Components\TextInput::make('sort_order')
                ->label('排序')
                ->numeric()
                ->default(fn (): int => Article::defaultSortOrderForNew())
                ->helperText('默认预填预计的文章 ID，保存后会自动对齐；数值越大越靠前，便于后续微调顺序')
                ->columnSpan(1),
            Forms\Components\Toggle::make('is_sticky')
                ->label('置顶')
                ->default(false)
                ->columnSpan(1),
            Forms\Components\Toggle::make('is_featured')
                ->label('是否精选')
                ->default(false)
                ->columnSpan(1),
            Forms\Components\Toggle::make('is_active')
                ->label('是否发布')
                ->default(true)
                ->columnSpan(1),
        ]);
    }

    private static function extraFieldsSection(?int $fixedCategoryId): Section
    {
        return Section::make('扩展字段')
            ->description(fn (Get $get): string => CategoryListTemplateRegistry::isVideoListCategoryId((int) ($fixedCategoryId ?? $get('category_id')))
                ? '视频栏目：请填写「视频文件名」（仅文件名，文件放在 public/assets/video/ 根目录）'
                : '字段定义来自所属栏目的「文章扩展字段」配置')
            ->schema(function (Get $get) use ($fixedCategoryId): array {
                $categoryId = $fixedCategoryId ?? $get('category_id');

                if (blank($categoryId)) {
                    return [
                        Forms\Components\Placeholder::make('extra_fields_hint')
                            ->label('')
                            ->content('请先选择所属栏目'),
                    ];
                }

                $category = Category::query()->find($categoryId);
                $components = ArticleExtraFields::articleFormComponents($category);

                if ($components === []) {
                    return [
                        Forms\Components\Placeholder::make('extra_fields_empty')
                            ->label('')
                            ->content('当前栏目未配置扩展字段'),
                    ];
                }

                return $components;
            })
            ->statePath('extra_fields')
            ->key(fn (Get $get): string => 'article-extra-fields-'.($fixedCategoryId ?? $get('category_id') ?? 'none'))
            ->columnSpanFull();
    }
}
