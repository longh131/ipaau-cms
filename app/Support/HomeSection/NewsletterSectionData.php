<?php

namespace App\Support\HomeSection;

use App\Support\RichContent;

class NewsletterSectionData
{
    /**
     * @return array{title: string, content: mixed, button_text: string}
     */
    public static function emptyStorage(): array
    {
        return [
            'title' => '',
            'content' => '',
            'button_text' => '提交',
        ];
    }

    /**
     * @return array{title: string, content: string, button_text: string}
     */
    public static function defaultStorage(): array
    {
        return [
            'title' => 'Subscribe to our newsletter',
            'content' => <<<'HTML'
<h1>Join our 2,000 subscribers to get the latest updates delivered straight to your inbox—no spam, just the good stuff.</h1>
<ul>
<li>Stay informed with the latest news, trends, and tips</li>
<li>Quick reads designed to fit your busy schedule</li>
<li>No spam—ever, just the good stuff</li>
</ul>
HTML,
            'button_text' => '提交',
        ];
    }

    /**
     * @param  array<string, mixed>|null  $data
     * @return array{title: string, content: array|string|null, button_text: string}
     */
    public static function forForm(?array $data, bool $nestedEditor = false): array
    {
        $data = is_array($data) ? $data : [];

        if (static::isLegacyFormat($data)) {
            $data = static::migrateLegacy($data);
        }

        $buttonText = trim((string) ($data['button_text'] ?? ''));
        $content = $nestedEditor
            ? RichContent::encodeDocumentForForm($data['content'] ?? '')
            : RichContent::toDocument($data['content'] ?? '');

        return [
            'title' => trim((string) ($data['title'] ?? '')),
            'content' => $content,
            'button_text' => $buttonText !== '' ? $buttonText : '提交',
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array{title: string, content: mixed, button_text: string}
     */
    public static function forStorage(array $data): array
    {
        $form = static::forForm($data);
        $content = static::normalizeContentForStorage($data['content'] ?? '');

        if ($form['title'] === '' && blank(strip_tags(RichContent::toHtml($content)))) {
            return static::emptyStorage();
        }

        return [
            'title' => $form['title'],
            'content' => $content,
            'button_text' => $form['button_text'],
        ];
    }

    /**
     * @param  array<string, mixed>|null  $data
     * @return array{title: string, content_html: string, button_text: string}
     */
    public static function forFrontend(?array $data): array
    {
        $stored = is_array($data) ? $data : [];

        if (static::isLegacyFormat($stored)) {
            $stored = static::migrateLegacy($stored);
        }

        $buttonText = trim((string) ($stored['button_text'] ?? ''));
        $contentHtml = RichContent::toHtml($stored['content'] ?? '');

        return [
            'title' => trim((string) ($stored['title'] ?? '')),
            'content_html' => blank(strip_tags($contentHtml)) ? '' : $contentHtml,
            'button_text' => $buttonText !== '' ? $buttonText : '提交',
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private static function isLegacyFormat(array $data): bool
    {
        return array_key_exists('subtitle', $data)
            && ! array_key_exists('content', $data);
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array{title: string, content: string, button_text: string}
     */
    private static function migrateLegacy(array $data): array
    {
        $title = trim((string) ($data['title'] ?? ''));
        $subtitle = trim((string) ($data['subtitle'] ?? ''));
        $content = $subtitle !== '' ? '<p>'.e($subtitle).'</p>' : '';

        $buttonText = trim((string) ($data['button_text'] ?? ''));

        return [
            'title' => $title,
            'content' => $content,
            'button_text' => $buttonText !== '' ? $buttonText : '提交',
        ];
    }

    private static function normalizeContentForStorage(mixed $content): mixed
    {
        $normalized = RichContent::normalizeState($content);

        if ($normalized === null) {
            return '';
        }

        if (is_array($normalized) && ($normalized['type'] ?? null) === 'doc') {
            return RichContent::normalizeDocument($normalized);
        }

        if (is_string($normalized)) {
            return trim($normalized);
        }

        return $normalized;
    }
}
