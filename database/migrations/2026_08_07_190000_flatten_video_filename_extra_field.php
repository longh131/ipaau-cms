<?php

use App\Models\Article;
use App\Support\CategoryListTemplate\VideoListTemplate;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Article::query()
            ->whereIn('category_id', VideoListTemplate::CATEGORY_IDS)
            ->orderBy('id')
            ->each(function (Article $article): void {
                $extraFields = is_array($article->extra_fields) ? $article->extra_fields : [];
                $current = (string) ($extraFields[VideoListTemplate::VIDEO_FILENAME_KEY] ?? '');
                $normalized = VideoListTemplate::normalizeVideoFilename($current);

                if ($normalized === '' || $normalized === $current) {
                    return;
                }

                $extraFields[VideoListTemplate::VIDEO_FILENAME_KEY] = $normalized;
                $article->extra_fields = $extraFields;
                $article->saveQuietly();
            });
    }

    public function down(): void
    {
        // 无法恢复原有子目录前缀。
    }
};
