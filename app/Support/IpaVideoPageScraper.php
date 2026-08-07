<?php

namespace App\Support;

use DOMDocument;
use DOMXPath;

class IpaVideoPageScraper
{
    /**
     * @return array<int, array{title: string, video_file: string, cover_path: ?string}>
     */
    public static function scrapeSection(string $html, string $sectionId): array
    {
        $sectionHtml = self::extractSectionHtml($html, $sectionId);

        if ($sectionHtml === '') {
            return [];
        }

        $items = [];
        $seen = [];

        if (! preg_match_all('/<div class="listSingle">\s*(.*?)\s*<\/div>/s', $sectionHtml, $blocks)) {
            return [];
        }

        foreach ($blocks[1] as $block) {
            $item = self::parseListSingle($block);

            if ($item === null) {
                continue;
            }

            $key = mb_strtolower($item['title']).'|'.mb_strtolower($item['video_file']);

            if (isset($seen[$key])) {
                continue;
            }

            $seen[$key] = true;
            $items[] = $item;
        }

        return $items;
    }

    private static function extractSectionHtml(string $html, string $sectionId): string
    {
        $pattern = '/<div class="videoList" id="'.preg_quote($sectionId, '/').'">(.*?)<div class="videoList" id="/s';

        if (! preg_match($pattern, $html, $matches)) {
            return '';
        }

        return $matches[1];
    }

    /**
     * @return array{title: string, video_file: string, cover_path: ?string}|null
     */
    private static function parseListSingle(string $block): ?array
    {
        $title = trim(preg_replace('/\s+/u', ' ', strip_tags($block)) ?? '');

        if ($title === '') {
            return null;
        }

        $videoFile = null;

        if (preg_match('/(?:src=|href=[\'"])([^\'"?&]+\.(?:mp4|mkv))/iu', $block, $matches)) {
            $videoFile = urldecode(html_entity_decode($matches[1], ENT_QUOTES | ENT_HTML5));
        }

        if ($videoFile === null || $videoFile === '') {
            return null;
        }

        $videoFile = ltrim(str_replace('\\', '/', $videoFile), '/');

        if (! str_contains($videoFile, '/')) {
            $videoFile = basename($videoFile);
        }

        $coverPath = null;

        if (preg_match('/<img[^>]+src=["\']([^"\']+)["\']/i', $block, $matches)) {
            $coverPath = html_entity_decode($matches[1], ENT_QUOTES | ENT_HTML5);
        }

        return [
            'title' => $title,
            'video_file' => $videoFile,
            'cover_path' => $coverPath,
        ];
    }
}
