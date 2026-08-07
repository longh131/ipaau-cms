<?php

namespace App\Support;

use Illuminate\Support\Str;

class VideoArticleContent
{
    public const PUBLIC_VIDEO_PREFIX = '/assets/video/';

    public static function buildHtml(string $publicVideoPath, ?string $publicPosterPath = null): string
    {
        $videoPath = self::normalizePublicPath($publicVideoPath);
        $poster = $publicPosterPath !== null ? self::normalizePublicPath($publicPosterPath) : null;
        $mime = self::guessMimeType($videoPath);
        $posterAttr = $poster !== null ? ' poster="'.e($poster).'"' : '';

        return '<div class="cms-video-embed"><video controls preload="metadata" playsinline class="cms-video-player w-full max-w-4xl mx-auto rounded-2xl"'.$posterAttr.'>'
            .'<source src="'.e($videoPath).'" type="'.e($mime).'">'
            .'</video></div>';
    }

    public static function rewriteLegacyAssetPath(string $path): string
    {
        $path = trim(html_entity_decode($path, ENT_QUOTES | ENT_HTML5));
        $path = ltrim(str_replace('\\', '/', $path), '/');

        if (str_starts_with($path, 'assets/video/')) {
            return $path;
        }

        if (str_starts_with($path, 'video/video_file/')) {
            return 'assets/video/'.substr($path, strlen('video/video_file/'));
        }

        if (str_starts_with($path, 'video/')) {
            return 'assets/video/'.substr($path, strlen('video/'));
        }

        if (str_starts_with($path, 'up/media/')) {
            return 'assets/video/'.basename($path);
        }

        if (str_starts_with($path, 'video_file/')) {
            return 'assets/video/'.substr($path, strlen('video_file/'));
        }

        return 'assets/video/'.basename($path);
    }

    public static function publicUrlFromStoredPath(string $storedPath): string
    {
        return self::normalizePublicPath($storedPath);
    }

    public static function normalizePublicPath(string $path): string
    {
        $path = trim($path);

        if ($path === '') {
            return '';
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        if (str_starts_with($path, '/assets/')) {
            return $path;
        }

        if (str_starts_with($path, 'assets/')) {
            return '/'.$path;
        }

        return self::PUBLIC_VIDEO_PREFIX.ltrim($path, '/');
    }

    public static function titleFromFilename(string $filename): string
    {
        $name = pathinfo($filename, PATHINFO_FILENAME);

        return trim((string) preg_replace('/^\d+\.\s*/u', '', $name));
    }

    public static function makeSlug(int $categoryId, string $title, ?string $suffix = null): string
    {
        $base = Str::slug($title);

        if ($base === '') {
            $base = 'video-'.substr(md5($title), 0, 12);
        }

        $slug = 'video-'.$categoryId.'-'.$base;

        if ($suffix !== null && $suffix !== '') {
            $slug .= '-'.Str::slug($suffix);
        }

        return Str::limit($slug, 200, '');
    }

    public static function extractVideoUrl(?string $html): ?string
    {
        if ($html === null || trim($html) === '') {
            return null;
        }

        if (preg_match('/<video\b[^>]*\bsrc=["\']([^"\']+)["\']/i', $html, $matches)) {
            return $matches[1];
        }

        if (preg_match('/<source\b[^>]*\bsrc=["\']([^"\']+)["\']/i', $html, $matches)) {
            return $matches[1];
        }

        return null;
    }

    public static function extractPosterUrl(?string $html): ?string
    {
        if ($html === null || trim($html) === '') {
            return null;
        }

        if (preg_match('/<video\b[^>]*\bposter=["\']([^"\']+)["\']/i', $html, $matches)) {
            return $matches[1];
        }

        return null;
    }

    public static function guessMimeType(string $path): string
    {
        $extension = strtolower(pathinfo(parse_url($path, PHP_URL_PATH) ?: $path, PATHINFO_EXTENSION));

        return match ($extension) {
            'webm' => 'video/webm',
            'mkv' => 'video/x-matroska',
            'mov' => 'video/quicktime',
            default => 'video/mp4',
        };
    }
}
