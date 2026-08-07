<?php

namespace App\Support\CategoryListTemplate;

use App\Models\Article;
use App\Models\Category;
use App\Support\RichContent;
use App\Support\VideoArticleContent;

class VideoListTemplate
{
    public const VIDEO_FILENAME_KEY = 'video_filename';

    public const BROADCAST_CATEGORY_ID = 113;

    public const RECAP_CATEGORY_ID = 114;

    /** @var array<int, string> */
    public const CATEGORY_IDS = [
        self::BROADCAST_CATEGORY_ID,
        self::RECAP_CATEGORY_ID,
    ];

    public static function isVideoCategory(?Category $category): bool
    {
        return $category !== null && CategoryListTemplateRegistry::isVideoList($category);
    }

    public static function isVideoCategoryId(?int $categoryId): bool
    {
        if ($categoryId === null || $categoryId <= 0) {
            return false;
        }

        $category = Category::query()->find($categoryId);

        return self::isVideoCategory($category);
    }

    /**
     * @param  array<string, mixed>|null  $extraFields
     */
    public static function normalizeVideoFilename(mixed $value): string
    {
        if (! is_string($value)) {
            return '';
        }

        $filename = trim(str_replace('\\', '/', $value));

        if ($filename === '') {
            return '';
        }

        $filename = preg_replace('#^/assets/video/#', '', $filename) ?? $filename;
        $filename = preg_replace('#^assets/video/#', '', $filename) ?? $filename;
        $filename = ltrim($filename, '/');

        return basename($filename);
    }

    /**
     * @param  array<string, mixed>|null  $extraFields
     */
    public static function videoFilenameFromExtraFields(?array $extraFields): string
    {
        if (! is_array($extraFields)) {
            return '';
        }

        return self::normalizeVideoFilename($extraFields[self::VIDEO_FILENAME_KEY] ?? '');
    }

    public static function videoPublicUrl(?string $videoFilename): ?string
    {
        $videoFilename = self::normalizeVideoFilename($videoFilename);

        if ($videoFilename === '') {
            return null;
        }

        return VideoArticleContent::normalizePublicPath(
            VideoArticleContent::PUBLIC_VIDEO_PREFIX.$videoFilename,
        );
    }

    public static function videoPublicUrlForArticle(Article $article): ?string
    {
        $fromExtra = self::videoPublicUrl(
            self::videoFilenameFromExtraFields($article->extra_fields),
        );

        if ($fromExtra !== null) {
            return $fromExtra;
        }

        $bodyHtml = RichContent::toHtml($article->content);
        $legacyUrl = VideoArticleContent::extractVideoUrl($bodyHtml);

        return filled($legacyUrl) ? VideoArticleContent::normalizePublicPath($legacyUrl) : null;
    }

    public static function posterPublicUrlForArticle(Article $article): ?string
    {
        if (filled($article->cover_image)) {
            return \App\Support\MediaUrl::resolve($article->cover_image);
        }

        $bodyHtml = RichContent::toHtml($article->content);

        return VideoArticleContent::extractPosterUrl($bodyHtml);
    }

    public static function videoMimeType(?string $publicUrl): string
    {
        return VideoArticleContent::guessMimeType($publicUrl ?? '');
    }

    /**
     * 从 legacy content HTML 或 cover_image 推断 video_filename（仅文件名，位于 assets/video/ 根目录）。
     */
    public static function inferVideoFilenameFromArticle(Article $article): string
    {
        $existing = self::videoFilenameFromExtraFields($article->extra_fields);

        if ($existing !== '') {
            return $existing;
        }

        $bodyHtml = RichContent::toHtml($article->content);
        $videoUrl = VideoArticleContent::extractVideoUrl($bodyHtml);

        if (filled($videoUrl)) {
            $path = parse_url($videoUrl, PHP_URL_PATH);

            if (is_string($path) && $path !== '') {
                return self::normalizeVideoFilename($path);
            }
        }

        if (filled($article->cover_image)) {
            $cover = str_replace('\\', '/', (string) $article->cover_image);
            $cover = preg_replace('#^assets/video/#', '', $cover) ?? $cover;
            $cover = ltrim($cover, '/');

            if ($cover !== '') {
                $mp4 = preg_replace('/\.(jpe?g|png|webp|gif)$/i', '.mp4', $cover);

                if ($mp4 !== $cover) {
                    return $mp4;
                }
            }
        }

        return '';
    }

    /**
     * @return array<int, array{key: string, type: string, label: string, show_in_list: bool}>
     */
    public static function defaultExtraFieldSchema(): array
    {
        return [
            [
                'key' => self::VIDEO_FILENAME_KEY,
                'type' => 'text',
                'label' => '视频文件名',
                'show_in_list' => false,
            ],
        ];
    }
}
