<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;

class MediaUrl
{
    /** @var array<int, string> */
    private const PUBLIC_DISK_PREFIXES = [
        'page-components/',
        'media/',
        'menu-promo/',
        'articles/',
        'rich-editor/',
        'settings/',
    ];

    public static function resolve(mixed $path): ?string
    {
        $relativePath = static::toRelativePath($path);

        if ($relativePath === '') {
            return null;
        }

        if (str_starts_with($relativePath, 'assets/')) {
            return asset($relativePath);
        }

        foreach (self::PUBLIC_DISK_PREFIXES as $prefix) {
            if (str_starts_with($relativePath, $prefix)) {
                return Storage::disk('public')->url($relativePath);
            }
        }

        return asset('storage/'.$relativePath);
    }

    public static function normalizeStoredPath(mixed $value): string
    {
        return static::toRelativePath($value);
    }

    /**
     * 将各种存储格式统一为相对路径（不含 storage/ 前缀）。
     * 支持：page-components/foo.png、/storage/page-components/foo.png、
     * http://ipaau-cms.test/storage/page-components/foo.png
     */
    public static function toRelativePath(mixed $path): string
    {
        if (is_array($path)) {
            $path = reset($path) ?: '';
        }

        $path = trim((string) $path);

        if ($path === '') {
            return '';
        }

        if (preg_match('#^https?://#i', $path)) {
            $parsed = parse_url($path);
            $path = $parsed['path'] ?? '';
        }

        $path = ltrim($path, '/');

        if (str_starts_with($path, 'storage/')) {
            $path = substr($path, 8);
        }

        return $path;
    }

    /**
     * 富文本 / 上传文件：统一为站点根相对路径 /storage/...
     */
    public static function toPublicStoragePath(mixed $path): ?string
    {
        $relativePath = static::toRelativePath($path);

        if ($relativePath === '') {
            return null;
        }

        return '/storage/'.$relativePath;
    }

    public static function isStorageUrl(?string $url): bool
    {
        if (blank($url)) {
            return false;
        }

        $url = trim($url);

        if (str_starts_with($url, '/storage/')) {
            return true;
        }

        if (preg_match('#^https?://[^/]+/storage/#i', $url)) {
            return true;
        }

        $relativePath = static::toRelativePath($url);

        if ($relativePath === '') {
            return false;
        }

        foreach (self::PUBLIC_DISK_PREFIXES as $prefix) {
            if (str_starts_with($relativePath, $prefix)) {
                return true;
            }
        }

        return false;
    }

    /**
     * 将富文本中的 storage 绝对 URL 转为 /storage/... 相对路径；外部链接保持不变。
     */
    public static function normalizeRichContentUrl(?string $url): ?string
    {
        if (blank($url)) {
            return null;
        }

        $url = trim($url);

        if (! static::isStorageUrl($url)) {
            return $url;
        }

        return static::toPublicStoragePath($url);
    }

    public static function normalizeRichContentHtml(string $html): string
    {
        if (! preg_match('/<img\b/i', $html) && ! preg_match('#/storage/#', $html)) {
            return $html;
        }

        $html = (string) preg_replace_callback(
            '/(<img\b[^>]*\bsrc=)(["\'])([^"\']+)\2/i',
            static function (array $matches): string {
                $normalized = static::normalizeRichContentUrl($matches[3]) ?? $matches[3];

                return $matches[1].$matches[2].$normalized.$matches[2];
            },
            $html,
        );

        $html = (string) preg_replace_callback(
            '/<img\b(?![^>]*\bsrc=)([^>]*\bdata-id=)(["\'])([^"\']+)\2/i',
            static function (array $matches): string {
                $src = static::normalizeRichContentUrl($matches[3])
                    ?? static::toPublicStoragePath($matches[3]);

                if (blank($src)) {
                    return $matches[0];
                }

                return '<img src="'.$src.'"'.$matches[1].$matches[2].$matches[3].$matches[2];
            },
            $html,
        );

        return (string) preg_replace_callback(
            '/\b(https?:\/\/[^"\s<>]+\/storage\/[^"\s<>]+)/i',
            static function (array $matches): string {
                return static::normalizeRichContentUrl($matches[1]) ?? $matches[1];
            },
            $html,
        );
    }

    /**
     * 递归规范化 JSON 文档、HTML 字符串或嵌套数组中的 storage 图片 URL。
     */
    public static function normalizeRichContentValue(mixed $value): mixed
    {
        if (is_string($value)) {
            $trimmed = trim($value);

            if ($trimmed === '') {
                return $value;
            }

            if (($trimmed[0] ?? '') === '{' || ($trimmed[0] ?? '') === '[') {
                $decoded = json_decode($trimmed, true);

                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    $normalized = static::normalizeRichContentValue($decoded);

                    return json_encode($normalized, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
                }
            }

            if (preg_match('/<\s*img\b/i', $value) || preg_match('#/storage/#', $value)) {
                return static::normalizeRichContentHtml($value);
            }

            if (static::isStorageUrl($value)) {
                return static::normalizeRichContentUrl($value);
            }

            return $value;
        }

        if (! is_array($value)) {
            return $value;
        }

        if (($value['type'] ?? null) === 'image' && isset($value['attrs']) && is_array($value['attrs'])) {
            if (isset($value['attrs']['src'])) {
                $value['attrs']['src'] = static::normalizeRichContentUrl((string) $value['attrs']['src']);
            }

            if (isset($value['attrs']['id'])) {
                $value['attrs']['id'] = static::toRelativePath($value['attrs']['id']);
            }

            return $value;
        }

        foreach ($value as $key => $item) {
            $value[$key] = static::normalizeRichContentValue($item);
        }

        return $value;
    }
}
