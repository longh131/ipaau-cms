<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ArticleCoverImageSelector
{
    /**
     * @return array<int, array{url: string, width: ?int}>
     */
    public static function extractImages(string $html): array
    {
        $images = [];

        if (! preg_match_all('/<img\b[^>]*>/i', $html, $tags)) {
            return [];
        }

        foreach ($tags[0] as $tag) {
            if (! preg_match('/\bsrc=["\']([^"\']+)["\']/i', $tag, $srcMatch)) {
                continue;
            }

            $width = null;

            if (preg_match('/\bwidth=["\']?(\d+)/i', $tag, $widthMatch)) {
                $width = (int) $widthMatch[1];
            }

            $images[] = [
                'url' => html_entity_decode($srcMatch[1], ENT_QUOTES | ENT_HTML5),
                'width' => $width,
            ];
        }

        return $images;
    }

    public static function selectCoverUrl(string $html): ?string
    {
        $images = self::extractImages($html);

        if ($images === []) {
            return null;
        }

        self::resolveMissingWidths($images, 2);

        $firstWidth = $images[0]['width'] ?? 0;

        if ($firstWidth > 600) {
            return $images[0]['url'];
        }

        return $images[1]['url'] ?? $images[0]['url'];
    }

    public static function selectFirstImageUrl(string $html): ?string
    {
        $images = self::extractImages($html);

        return $images[0]['url'] ?? null;
    }

    /**
     * @param  array<int, array{url: string, width: ?int}>  $images
     */
    private static function resolveMissingWidths(array &$images, int $limit): void
    {
        foreach (array_slice($images, 0, $limit, true) as $index => $image) {
            if (($image['width'] ?? 0) > 0) {
                continue;
            }

            $images[$index]['width'] = self::probeImageWidth($image['url']);
        }
    }

    private static function probeImageWidth(string $url): ?int
    {
        $bytes = self::fetchBytes($url);

        if ($bytes === null) {
            return null;
        }

        if (! function_exists('getimagesizefromstring')) {
            return null;
        }

        $size = @getimagesizefromstring($bytes);

        return is_array($size) ? (int) ($size[0] ?? 0) : null;
    }

    public static function downloadCover(string $url): ?string
    {
        $url = trim($url);

        if ($url === '') {
            return null;
        }

        $bytes = self::fetchBytes($url);

        if ($bytes === null) {
            return null;
        }

        $extension = self::guessExtension($url, $bytes);
        $relativePath = 'articles/covers/'.Str::ulid().'.'.$extension;

        if ($extension === 'jpg' && function_exists('imagecreatefromstring')) {
            $image = @imagecreatefromstring($bytes);

            if ($image !== false) {
                ob_start();
                imagejpeg($image, null, 88);
                $bytes = ob_get_clean() ?: $bytes;
                imagedestroy($image);
            }
        }

        Storage::disk('public')->put($relativePath, $bytes);

        return $relativePath;
    }

    private static function fetchBytes(string $url): ?string
    {
        if (str_starts_with($url, '//')) {
            $url = 'https:'.$url;
        }

        $context = stream_context_create([
            'http' => [
                'header' => "User-Agent: Mozilla/5.0\r\n",
                'timeout' => 45,
            ],
            'ssl' => [
                'verify_peer' => true,
                'verify_peer_name' => true,
            ],
        ]);

        $bytes = @file_get_contents($url, false, $context);

        return $bytes === false ? null : $bytes;
    }

    private static function guessExtension(string $url, string $bytes): string
    {
        $path = strtolower(parse_url($url, PHP_URL_PATH) ?: '');
        $extension = pathinfo($path, PATHINFO_EXTENSION);

        if (in_array($extension, ['jpg', 'jpeg', 'png', 'webp', 'gif'], true)) {
            return $extension === 'jpeg' ? 'jpg' : $extension;
        }

        if (function_exists('getimagesizefromstring')) {
            $mime = @getimagesizefromstring($bytes)['mime'] ?? null;

            return match ($mime) {
                'image/png' => 'png',
                'image/webp' => 'webp',
                'image/gif' => 'gif',
                default => 'jpg',
            };
        }

        return 'jpg';
    }
}
