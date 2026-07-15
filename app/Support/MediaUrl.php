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
}
