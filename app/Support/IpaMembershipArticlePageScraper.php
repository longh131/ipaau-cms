<?php

namespace App\Support;

use Carbon\Carbon;
use DOMDocument;
use DOMXPath;

class IpaMembershipArticlePageScraper
{
    public const BASE_URL = 'https://www.ipaau.org.cn';

    /**
     * @return array<int, array{legacy_id: string, url: string, title: string, published_at: ?Carbon}>
     */
    public static function scrapeListItems(string $html): array
    {
        $items = [];
        $seen = [];

        if (! preg_match_all(
            '/<div class="list">\s*<a href="([^"]+)">(.*?)<\/a>\s*<span[^>]*>\s*([^<]+?)\s*<\/span>/su',
            $html,
            $matches,
            PREG_SET_ORDER,
        )) {
            return [];
        }

        foreach ($matches as $match) {
            $url = self::absoluteUrl(html_entity_decode($match[1], ENT_QUOTES | ENT_HTML5));
            $title = trim(html_entity_decode(strip_tags($match[2]), ENT_QUOTES | ENT_HTML5));
            $title = trim(preg_replace('/\s+/u', ' ', $title) ?? '');

            if ($title === '' || ! preg_match('/[?&]id=(\d+)/', $url, $idMatch)) {
                continue;
            }

            $legacyId = $idMatch[1];
            $key = $legacyId;

            if (isset($seen[$key])) {
                continue;
            }

            $seen[$key] = true;

            $items[] = [
                'legacy_id' => $legacyId,
                'url' => $url,
                'title' => $title,
                'published_at' => self::parseDate(trim($match[3])),
            ];
        }

        return $items;
    }

    public static function scrapeMaxPageNumber(string $html): int
    {
        $maxPage = 1;

        if (preg_match_all('/[?&]page=(\d+)/', $html, $matches)) {
            foreach ($matches[1] as $page) {
                $pageNumber = (int) $page;

                if ($pageNumber > $maxPage) {
                    $maxPage = $pageNumber;
                }
            }
        }

        return max(1, $maxPage);
    }

    /**
     * @return array{title: ?string, published_at: ?Carbon, content_html: string}
     */
    public static function scrapeDetail(string $html): array
    {
        $contentHtml = self::extractRightContentHtml($html);
        $title = self::extractDetailTitle($html);
        $publishedAt = self::extractDetailDate($html);

        return [
            'title' => $title,
            'published_at' => $publishedAt,
            'content_html' => self::normalizeDetailContent($contentHtml),
        ];
    }

    public static function normalizeDetailContent(string $html): string
    {
        $html = trim($html);

        if ($html === '') {
            return '';
        }

        $html = preg_replace('/\s*(?:<br\s*\/?>\s*){3,}/i', '<br><br>', $html) ?? $html;
        $html = preg_replace('/<embed\b[^>]*src=(["\'])([^"\']+)\1[^>]*>/iu', '<p><a href="$2" target="_blank" rel="noopener">查看 PDF 文档</a></p>', $html) ?? $html;
        $html = preg_replace('/<object\b[^>]*data=(["\'])([^"\']+)\1[^>]*>.*?<\/object>/isu', '<p><a href="$2" target="_blank" rel="noopener">查看 PDF 文档</a></p>', $html) ?? $html;

        $rewriteResult = LegacyNewsImageUrlRewriter::rewriteContent($html);

        return trim($rewriteResult['content']);
    }

    public static function absoluteUrl(string $url): string
    {
        $url = trim(html_entity_decode($url, ENT_QUOTES | ENT_HTML5));

        if ($url === '') {
            return self::BASE_URL;
        }

        if (preg_match('#^https?://#i', $url)) {
            return $url;
        }

        if (str_starts_with($url, '//')) {
            return 'https:'.$url;
        }

        return rtrim(self::BASE_URL, '/').'/'.ltrim($url, '/');
    }

    public static function buildListUrl(string $section, int $page): string
    {
        $section = trim($section, '/');

        return self::BASE_URL.'/membership/'.$section.'/?page='.$page;
    }

    private static function extractRightContentHtml(string $html): string
    {
        $dom = new DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML('<?xml encoding="utf-8" ?>'.$html, LIBXML_NOWARNING | LIBXML_NOERROR);
        libxml_clear_errors();

        $xpath = new DOMXPath($dom);
        $node = $xpath->query('//*[@id="right_content"]')->item(0);

        if ($node === null) {
            return '';
        }

        $innerHtml = '';

        foreach ($node->childNodes as $child) {
            $innerHtml .= $dom->saveHTML($child);
        }

        return trim($innerHtml);
    }

    private static function extractDetailTitle(string $html): ?string
    {
        if (! preg_match('/<div class="content-title"[^>]*>(.*?)<\/div>\s*<br/su', $html, $match)) {
            return null;
        }

        $titleBlock = preg_replace('/<div[^>]*>.*?<\/div>/su', '', $match[1]) ?? $match[1];
        $title = trim(html_entity_decode(strip_tags($titleBlock), ENT_QUOTES | ENT_HTML5));
        $title = trim(preg_replace('/\s+/u', ' ', $title) ?? '');

        return $title !== '' ? $title : null;
    }

    private static function extractDetailDate(string $html): ?Carbon
    {
        if (! preg_match('/<div class="content-title"[^>]*>.*?<div[^>]*>\s*([^<]+?)\s*<\/div>/su', $html, $match)) {
            return null;
        }

        return self::parseDate(trim($match[1]));
    }

    private static function parseDate(string $value): ?Carbon
    {
        $value = trim($value);

        if ($value === '') {
            return null;
        }

        try {
            return Carbon::parse($value)->startOfDay();
        } catch (\Throwable) {
            return null;
        }
    }
}
