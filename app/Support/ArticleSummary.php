<?php

namespace App\Support;

class ArticleSummary
{
    public static function fromContent(mixed $content, ?string $fallbackTitle = null, int $maxLength = 100): string
    {
        $html = RichContent::toHtml($content);
        $text = html_entity_decode(strip_tags($html), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = iconv('UTF-8', 'UTF-8//IGNORE', $text) ?: '';
        $text = trim((string) preg_replace('/\s+/u', ' ', $text));

        if ($text === '' && filled($fallbackTitle)) {
            $text = iconv('UTF-8', 'UTF-8//IGNORE', trim($fallbackTitle)) ?: '';
        }

        if ($text === '') {
            return '';
        }

        return static::truncate($text, $maxLength);
    }

    private static function truncate(string $text, int $maxLength): string
    {
        if (mb_strlen($text) <= $maxLength) {
            return $text;
        }

        $suffix = '…';
        $budget = max(1, $maxLength - mb_strlen($suffix));
        $chars = mb_str_split($text, 1, 'UTF-8') ?: [];
        $excerpt = implode('', array_slice($chars, 0, $budget));

        return $excerpt.$suffix;
    }
}
