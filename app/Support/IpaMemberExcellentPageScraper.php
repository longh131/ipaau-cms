<?php

namespace App\Support;

use DOMDocument;
use DOMXPath;

class IpaMemberExcellentPageScraper
{
    /**
     * @return array<int, array{
     *     title: string,
     *     position: ?string,
     *     summary: ?string,
     *     image_url: ?string,
     *     detail_url: ?string,
     *     detail_legacy_id: ?string
     * }>
     */
    public static function scrapeMembers(string $html): array
    {
        $contentHtml = self::extractRightContentHtml($html);

        if ($contentHtml === '') {
            return [];
        }

        $blocks = self::splitMemberBlocks($contentHtml);
        $members = [];

        foreach ($blocks as $block) {
            $member = self::parseMemberBlock($block);

            if ($member !== null) {
                $members[] = $member;
            }
        }

        return $members;
    }

    /**
     * @return array<int, string>
     */
    private static function splitMemberBlocks(string $html): array
    {
        $found = [];

        $patterns = [
            '/<table width="680" border="0" cellspacing="0" cellpadding="0" bgcolor="#484E41">.*?<\/table>/su',
            '/<table>\s*<tbody>\s*<tr>\s*<td width="380"[^>]*>.*?<\/table>/su',
        ];

        foreach ($patterns as $pattern) {
            if (! preg_match_all($pattern, $html, $matches, PREG_OFFSET_CAPTURE)) {
                continue;
            }

            foreach ($matches[0] as $match) {
                $found[] = [
                    'offset' => $match[1],
                    'html' => $match[0],
                ];
            }
        }

        usort($found, fn (array $left, array $right): int => $left['offset'] <=> $right['offset']);

        return array_values(array_map(fn (array $item): string => $item['html'], $found));
    }

    /**
     * @return array{
     *     title: string,
     *     position: ?string,
     *     summary: ?string,
     *     image_url: ?string,
     *     detail_url: ?string,
     *     detail_legacy_id: ?string
     * }|null
     */
    private static function parseMemberBlock(string $block): ?array
    {
        $imageUrl = self::extractImageUrl($block);
        $detailUrl = self::extractDetailUrl($block);
        $detailLegacyId = self::extractLegacyId($detailUrl);
        $titleLines = self::extractTitleLines($block);

        if ($titleLines === []) {
            return null;
        }

        $title = trim($titleLines[0]);
        $positionLines = array_values(array_filter(array_map('trim', array_slice($titleLines, 1)), fn (string $line): bool => $line !== ''));
        $position = $positionLines !== [] ? implode("\n", $positionLines) : null;
        $summary = self::extractSummary($block);

        if ($title === '') {
            return null;
        }

        return [
            'title' => $title,
            'position' => $position,
            'summary' => $summary !== '' ? $summary : null,
            'image_url' => $imageUrl,
            'detail_url' => $detailUrl,
            'detail_legacy_id' => $detailLegacyId,
        ];
    }

    private static function extractImageUrl(string $block): ?string
    {
        if (! preg_match('/\bsrc=(["\'])([^"\']*(?:uploadfile|uploadfiles)[^"\']+)\1/i', $block, $match)) {
            return null;
        }

        $url = html_entity_decode($match[2], ENT_QUOTES | ENT_HTML5);

        return IpaMembershipArticlePageScraper::absoluteUrl($url);
    }

    private static function extractDetailUrl(string $block): ?string
    {
        if (! preg_match('/\bhref=(["\'])([^"\']*(?:pointofview|interview)\/view\.aspx\?id=\d+[^"\']*)\1/i', $block, $match)) {
            return null;
        }

        return IpaMembershipArticlePageScraper::absoluteUrl(html_entity_decode($match[2], ENT_QUOTES | ENT_HTML5));
    }

    private static function extractLegacyId(?string $url): ?string
    {
        if ($url === null || ! preg_match('/[?&]id=(\d+)/', $url, $match)) {
            return null;
        }

        return $match[1];
    }

    /**
     * @return array<int, string>
     */
    private static function extractTitleLines(string $block): array
    {
        if (! preg_match('/<p[^>]*font-size:\s*16px[^>]*>(.*?)<\/p>/su', $block, $match)) {
            return [];
        }

        $titleHtml = preg_replace('/<\/?strong[^>]*>/iu', '', $match[1]) ?? $match[1];
        $titleHtml = str_ireplace(['<br/>', '<br />', '<br>'], "\n", $titleHtml);
        $text = html_entity_decode(strip_tags($titleHtml), ENT_QUOTES | ENT_HTML5);
        $lines = preg_split("/\R/u", $text) ?: [];

        return array_values(array_filter(array_map('trim', $lines), fn (string $line): bool => $line !== ''));
    }

    private static function extractSummary(string $block): string
    {
        if (preg_match('/<p[^>]*text-align:\s*justify[^>]*>(.*?)<\/p>/su', $block, $match)) {
            return self::normalizeSummaryHtml($match[1]);
        }

        $remaining = preg_replace('/<p[^>]*font-size:\s*16px[^>]*>.*?<\/p>/su', '', $block, 1) ?? $block;

        if (preg_match('/<p[^>]*>(.*?)<\/p>/su', $remaining, $match)) {
            return self::normalizeSummaryHtml($match[1]);
        }

        return '';
    }

    private static function normalizeSummaryHtml(string $html): string
    {
        $html = preg_replace('/<a\b[^>]*>.*?<\/a>/su', '', $html) ?? $html;
        $text = html_entity_decode(strip_tags($html), ENT_QUOTES | ENT_HTML5);
        $text = trim(preg_replace('/\s+/u', ' ', $text) ?? '');

        return $text;
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
}
