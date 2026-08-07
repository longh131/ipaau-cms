<?php

use App\Models\Article;
use App\Models\Category;
use App\Support\CategoryListTemplate\VideoListTemplate;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        foreach (VideoListTemplate::CATEGORY_IDS as $categoryId) {
            $category = Category::query()->find($categoryId);

            if ($category === null) {
                continue;
            }

            $schema = is_array($category->article_extra_field_schema)
                ? $category->article_extra_field_schema
                : [];

            $hasField = collect($schema)->contains(
                fn (mixed $field): bool => is_array($field)
                    && ($field['key'] ?? '') === VideoListTemplate::VIDEO_FILENAME_KEY,
            );

            if (! $hasField) {
                $schema[] = VideoListTemplate::defaultExtraFieldSchema()[0];
                $category->article_extra_field_schema = $schema;
                $category->save();
            }
        }

        Article::query()
            ->whereIn('category_id', VideoListTemplate::CATEGORY_IDS)
            ->orderBy('id')
            ->each(function (Article $article): void {
                $filename = VideoListTemplate::inferVideoFilenameFromArticle($article);

                if ($filename === '') {
                    return;
                }

                $extraFields = is_array($article->extra_fields) ? $article->extra_fields : [];
                $extraFields[VideoListTemplate::VIDEO_FILENAME_KEY] = $filename;

                $article->extra_fields = $extraFields;
                $article->saveQuietly();
            });
    }

    public function down(): void
    {
        // 保留扩展字段配置与已迁移数据。
    }
};
