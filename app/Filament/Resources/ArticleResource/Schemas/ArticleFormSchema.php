<?php

namespace App\Filament\Resources\ArticleResource\Schemas;

use App\Models\Category;
use App\Support\ArticleExtraFields;
use App\Support\RichContent;
use Filament\Forms;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class ArticleFormSchema
{
    public static function configure(Schema $schema, ?int $fixedCategoryId = null): Schema
    {
        return $schema->components([
            Forms\Components\TextInput::make('title')
                ->label('标题')
                ->required(),
            Forms\Components\TextInput::make('slug')
                ->label('别名')
                ->required()
                ->unique(ignoreRecord: true),
            Forms\Components\Select::make('category_id')
                ->label('所属栏目')
                ->options(fn (): array => Category::query()
                    ->where('type', 'article')
                    ->orderBy('sort_order')
                    ->pluck('name', 'id')
                    ->all())
                ->searchable()
                ->required()
                ->default($fixedCategoryId)
                ->disabled($fixedCategoryId !== null)
                ->dehydrated(true)
                ->live(),
            Forms\Components\TextInput::make('redirect_url')
                ->label('跳转链接')
                ->placeholder('如果填写了链接，点击文章时将跳转到外部链接')
                ->url(),
            Forms\Components\Textarea::make('summary')
                ->label('文章摘要')
                ->rows(3)
                ->columnSpanFull(),
            RichContent::configureFileAttachments(
                Forms\Components\RichEditor::make('content')
                    ->label('内容')
                    ->required()
                    ->columnSpanFull()
                    ->toolbarButtons(RichContent::pageToolbar())
                    ->helperText(RichContent::imageUploadHelperText()),
            ),
            Forms\Components\DateTimePicker::make('published_at')
                ->label('发布时间')
                ->columnSpan(1),
            Forms\Components\TextInput::make('sort_order')
                ->label('排序')
                ->numeric()
                ->default(0)
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
            Section::make('扩展字段')
                ->description('字段定义来自所属栏目的「文章扩展字段」配置')
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
                ->columnSpanFull(),
        ]);
    }
}
