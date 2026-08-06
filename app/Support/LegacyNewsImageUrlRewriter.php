<?php

namespace App\Support;

class LegacyNewsImageUrlRewriter
{
    public const LEGACY_PUBLIC_PREFIX = '/assets/img/ipa-news-legacy/';

    /**
     * @return array{content: string, changed: bool, replacements: int}
     */
    public static function rewriteContent(string $content): array
    {
        $replacements = 0;

        $rewritten = preg_replace_callback(
            '/\bsrc=(["\'])([^"\']+)\1/i',
            function (array $matches) use (&$replacements): string {
                $quote = $matches[1];
                $original = html_entity_decode($matches[2], ENT_QUOTES | ENT_HTML5);
                $rewrittenUrl = self::rewriteUrl($original);

                if ($rewrittenUrl === null) {
                    return $matches[0];
                }

                $replacements++;

                return 'src='.$quote.$rewrittenUrl.$quote;
            },
            $content,
        );

        return [
            'content' => is_string($rewritten) ? $rewritten : $content,
            'changed' => $replacements > 0,
            'replacements' => $replacements,
        ];
    }

    public static function rewriteUrl(string $url): ?string
    {
        $url = trim(html_entity_decode($url, ENT_QUOTES | ENT_HTML5));

        if ($url === '') {
            return null;
        }

        if (str_starts_with($url, self::LEGACY_PUBLIC_PREFIX)) {
            return null;
        }

        $filename = self::extractFilename($url);

        if ($filename === null) {
            return null;
        }

        return self::LEGACY_PUBLIC_PREFIX.$filename;
    }

    private static function extractFilename(string $url): ?string
    {
        $path = $url;

        if (preg_match('#^https?://#i', $url)) {
            $path = parse_url($url, PHP_URL_PATH) ?: '';
        }

        $path = str_replace('\\', '/', $path);
        $path = preg_replace('#/+#', '/', $path) ?? $path;

        while (str_contains($path, '/../')) {
            $path = preg_replace('#/[^/]+/\.\./#', '/', $path) ?? $path;
        }

        if (preg_match('~/(?:uploadfile|uploadfiles)/([^/?#]+)$~i', $path, $matches)
            || preg_match('~/(?:uploadfile|uploadfiles)/([^/?#]+)$~i', '/'.ltrim($path, '/'), $matches)) {
            $filename = basename($matches[1]);

            return $filename !== '' ? $filename : null;
        }

        $basename = basename($path);

        if ($basename !== '' && preg_match('/\.(?:jpe?g|png|gif|webp|bmp)$/i', $basename)) {
            return $basename;
        }

        return null;
    }

    public static function toStoredCoverPath(?string $rewrittenUrl): ?string
    {
        if ($rewrittenUrl === null || trim($rewrittenUrl) === '') {
            return null;
        }

        $path = ltrim(trim($rewrittenUrl), '/');

        return $path !== '' ? $path : null;
    }
}
