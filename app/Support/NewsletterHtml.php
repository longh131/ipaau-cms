<?php

namespace App\Support;

class NewsletterHtml
{
    public static function rewriteAssetUrls(string $html, string $assetDir): string
    {
        $base = '/assets/newsletter/'.trim(str_replace('\\', '/', $assetDir), '/').'/';

        $html = str_replace('/member/myaccount/newsLetter/', '/assets/newsletter/', $html);

        $html = preg_replace_callback(
            '/\s(?P<attr>src|href)=([\'"])(?!https?:|\/|mailto:|#|data:|tel:)(?P<url>[^\'"]+)\2/i',
            function (array $matches) use ($base): string {
                $url = ltrim($matches['url'], './');

                return ' '.$matches['attr'].'='.$matches[2].$base.$url.$matches[2];
            },
            $html,
        ) ?? $html;

        return $html;
    }

    public static function resolveLocalFile(string $newsletterRoot, string $relativePath): ?string
    {
        $relativePath = str_replace('\\', '/', $relativePath);
        $fullPath = rtrim($newsletterRoot, '/\\').'/'.$relativePath;

        if (is_file($fullPath)) {
            return $fullPath;
        }

        $directory = dirname($fullPath);
        $extension = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));

        if (! is_dir($directory) || $extension === '') {
            return null;
        }

        $candidates = glob($directory.'/*.'.$extension) ?: [];

        return $candidates[0] ?? null;
    }

    public static function readHtmlFile(string $path): string
    {
        $raw = file_get_contents($path) ?: '';

        return static::convertToUtf8($raw);
    }

    public static function convertToUtf8(string $html): string
    {
        if (preg_match('/charset\s*=\s*["\']?\s*([\w-]+)/i', $html, $matches)) {
            $charset = strtoupper(str_replace([' ', '-'], '', $matches[1]));

            if (! in_array($charset, ['UTF8', 'UTF'], true)) {
                $source = match ($charset) {
                    'GB2312', 'GBK' => 'GB18030',
                    default => $matches[1],
                };

                $converted = @iconv($source, 'UTF-8//IGNORE', $html);

                if ($converted !== false) {
                    $html = $converted;
                }
            }
        }

        if (! mb_check_encoding($html, 'UTF-8')) {
            $converted = @iconv('GB18030', 'UTF-8//IGNORE', $html);
            $html = $converted !== false ? $converted : $html;
        }

        return preg_replace('/charset\s*=\s*["\']?\s*[\w-]+/i', 'charset=UTF-8', $html, 1) ?? $html;
    }

    public static function publicAssetUrl(string $newsletterPublicRoot, string $relativePath): string
    {
        $relativePath = str_replace('\\', '/', ltrim($relativePath, '/'));

        return '/'.trim($newsletterPublicRoot, '/').'/'.$relativePath;
    }
}
