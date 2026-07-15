<?php

namespace App\Support;

use Carbon\Carbon;

class NewsletterCatalog
{
    /**
     * @return array<int, array{
     *     href: string,
     *     relative_path: string,
     *     title: string,
     *     issue_number: ?int,
     *     published_at: ?Carbon,
     *     asset_dir: string,
     *     extension: string
     * }>
     */
    public static function parse(string $indexPath): array
    {
        $html = file_get_contents($indexPath);
        preg_match_all('/<a\s+href="([^"]*newsLetter[^"]+)"[^>]*>(.*?)<\/a>/is', $html, $matches, PREG_SET_ORDER);

        $entries = [];

        foreach ($matches as $match) {
            $href = html_entity_decode(trim($match[1]));
            $path = (string) parse_url($href, PHP_URL_PATH);
            $relativePath = ltrim(str_replace('/member/myaccount/newsLetter/', '', $path), '/');
            $title = trim((string) preg_replace('/\s+/u', ' ', strip_tags($match[2])));

            if ($relativePath === '' || $title === '') {
                continue;
            }

            $extension = strtolower(pathinfo($relativePath, PATHINFO_EXTENSION));
            $assetDir = str_replace('\\', '/', dirname($relativePath));
            if ($assetDir === '.') {
                $assetDir = pathinfo($relativePath, PATHINFO_FILENAME);
            }

            $entries[] = [
                'href' => $href,
                'relative_path' => $relativePath,
                'title' => $title,
                'issue_number' => static::parseIssueNumber($title),
                'published_at' => static::parsePublishedAt($relativePath, $title),
                'asset_dir' => $assetDir,
                'extension' => $extension,
            ];
        }

        return $entries;
    }

    public static function parseIssueNumber(string $title): ?int
    {
        if (preg_match('/总第(\d+)期/u', $title, $matches)) {
            return (int) $matches[1];
        }

        return null;
    }

    public static function parsePublishedAt(string $relativePath, string $title): ?Carbon
    {
        $segments = explode('/', str_replace('\\', '/', $relativePath));

        foreach ($segments as $segment) {
            if (preg_match('/^(20\d{2})(\d{2})$/', $segment, $matches)) {
                return Carbon::create((int) $matches[1], (int) $matches[2], 1)->startOfDay();
            }

            if (preg_match('/^(20\d{2})$/', $segment, $matches)) {
                if (preg_match('/(\d{4})年(\d{1,2})月/u', $title, $titleMatches)) {
                    return Carbon::create((int) $titleMatches[1], (int) $titleMatches[2], 1)->startOfDay();
                }

                return Carbon::create((int) $matches[1], 1, 1)->startOfDay();
            }
        }

        if (preg_match('/(\d{4})年(\d{1,2})月/u', $title, $matches)) {
            return Carbon::create((int) $matches[1], (int) $matches[2], 1)->startOfDay();
        }

        return null;
    }

    public static function makeSlug(string $relativePath, ?int $issueNumber): string
    {
        $pathSlug = strtolower(str_replace(['/', '\\', '.'], '-', pathinfo($relativePath, PATHINFO_DIRNAME).'-'.pathinfo($relativePath, PATHINFO_FILENAME)));
        $pathSlug = trim(preg_replace('/-+/', '-', $pathSlug) ?? '', '-');

        if ($issueNumber !== null) {
            return 'newsletter-'.$issueNumber.'-'.$pathSlug;
        }

        return 'newsletter-'.$pathSlug;
    }
}
