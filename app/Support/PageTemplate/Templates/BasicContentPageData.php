<?php

namespace App\Support\PageTemplate\Templates;

use App\Models\Page;
use App\Support\RichContent;

class BasicContentPageData
{
    /**
     * @return array{heading: string, summary: string, body: string}
     */
    public static function emptyStorage(): array
    {
        return [
            'heading' => '',
            'summary' => '',
            'body' => '',
        ];
    }

    /**
     * @param  array<string, mixed>|null  $data
     * @return array{heading: string, summary: string, body: string}
     */
    public static function forForm(?array $data): array
    {
        $data = is_array($data) ? $data : [];

        return [
            'heading' => trim((string) ($data['heading'] ?? '')),
            'summary' => trim((string) ($data['summary'] ?? '')),
            'body' => static::normalizeBodyForForm($data['body'] ?? ''),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array{heading: string, summary: string, body: string}
     */
    public static function forStorage(array $data): array
    {
        $form = static::forForm($data);

        return [
            'heading' => $form['heading'],
            'summary' => $form['summary'],
            'body' => trim($form['body']),
        ];
    }

    /**
     * @param  array<string, mixed>|null  $data
     * @return array{
     *     heading: string,
     *     summary: string,
     *     body_html: string,
     *     has_content: bool
     * }
     */
    public static function forFrontend(?array $data, Page $page): array
    {
        $form = static::forForm($data);
        $heading = filled($form['heading']) ? $form['heading'] : $page->displayTitle();
        $bodyHtml = trim($form['body']);

        return [
            'heading' => $heading,
            'summary' => $form['summary'],
            'body_html' => $bodyHtml,
            'has_content' => static::hasContent($form, $page),
        ];
    }

    /**
     * @param  array<string, mixed>|null  $data
     */
    public static function hasContent(?array $data, ?Page $page = null): bool
    {
        $data = static::forForm($data);

        if (filled($data['summary'])) {
            return true;
        }

        if (filled(strip_tags($data['body']))) {
            return true;
        }

        if (filled($data['heading'])) {
            return true;
        }

        return filled($page?->displayTitle());
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public static function contentSnapshot(array $data): string
    {
        $parts = [];
        $form = static::forForm($data);

        if (filled($form['summary'])) {
            $parts[] = '<p>'.e($form['summary']).'</p>';
        }

        if (filled(strip_tags($form['body']))) {
            $parts[] = $form['body'];
        }

        return implode("\n", $parts);
    }

    /**
     * 表单编辑区：纯 HTML 字符串原样展示；旧版富文本 JSON 自动转为 HTML。
     */
    public static function normalizeBodyForForm(mixed $body): string
    {
        if (blank($body)) {
            return '';
        }

        if (is_array($body) && ($body['type'] ?? null) === 'doc') {
            return RichContent::toHtml($body);
        }

        if (! is_string($body)) {
            return RichContent::toHtml($body);
        }

        $trimmed = trim($body);

        if ($trimmed === '') {
            return '';
        }

        if (str_starts_with($trimmed, '{') || str_starts_with($trimmed, '[')) {
            $decoded = json_decode($trimmed, true);

            if (is_array($decoded) && ($decoded['type'] ?? null) === 'doc') {
                return RichContent::toHtml($decoded);
            }
        }

        return $trimmed;
    }
}
