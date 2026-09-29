<?php

use App\Models\Article;
use App\Support\CategoryListTemplate\VideoListTemplate;
use App\Support\VideoArticleContent;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Article::query()
            ->where(function ($query): void {
                $query->where('cover_image', 'like', 'assets/video/%/%')
                    ->orWhere('cover_image', 'like', '/assets/video/%/%')
                    ->orWhere('content', 'like', '%/assets/video/%/%')
                    ->orWhere('content', 'like', '%assets/video/%/%');
            })
            ->orderBy('id')
            ->each(function (Article $article): void {
                $dirty = false;

                if (filled($article->cover_image)) {
                    $flattenedCover = VideoArticleContent::flattenVideoRootPath((string) $article->cover_image);

                    if ($flattenedCover !== (string) $article->cover_image) {
                        $article->cover_image = $flattenedCover;
                        $dirty = true;
                    }
                }

                if (is_string($article->content) && $article->content !== '') {
                    $flattenedContent = VideoArticleContent::flattenVideoPathsInText($article->content);

                    if ($flattenedContent !== $article->content) {
                        $article->content = $flattenedContent;
                        $dirty = true;
                    }
                }

                $extraFields = is_array($article->extra_fields) ? $article->extra_fields : [];
                $currentFilename = (string) ($extraFields[VideoListTemplate::VIDEO_FILENAME_KEY] ?? '');
                $normalizedFilename = VideoListTemplate::normalizeVideoFilename($currentFilename);

                if ($normalizedFilename !== '' && $normalizedFilename !== $currentFilename) {
                    $extraFields[VideoListTemplate::VIDEO_FILENAME_KEY] = $normalizedFilename;
                    $article->extra_fields = $extraFields;
                    $dirty = true;
                }

                if ($dirty) {
                    $article->saveQuietly();
                }
            });
    }

    public function down(): void
    {
        // 无法可靠恢复 IPA播报 / IPA活动回顾 等子目录前缀。
    }
};
