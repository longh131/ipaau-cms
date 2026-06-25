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
    ];

    public static function resolve(mixed $path): ?string
    {
        if (is_array($path)) {
            $path = reset($path) ?: '';
        }

        $path = trim((string) $path);

        if ($path === '') {
            return null;
        }

        if (preg_match('#^https?://#i', $path)) {
            return $path;
        }

        $path = ltrim($path, '/');

        if (str_starts_with($path, 'storage/')) {
            return asset($path);
        }

        foreach (self::PUBLIC_DISK_PREFIXES as $prefix) {
            if (str_starts_with($path, $prefix)) {
                return Storage::disk('public')->url($path);
            }
        }

        return asset($path);
    }

    public static function normalizeStoredPath(mixed $value): string
    {
        if (is_array($value)) {
            $value = reset($value) ?: '';
        }

        return trim((string) $value);
    }
}
