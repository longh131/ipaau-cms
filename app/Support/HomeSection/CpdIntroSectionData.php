<?php

namespace App\Support\HomeSection;

use App\Support\RichContent;

class CpdIntroSectionData
{
    /**
     * @return array{content: null}
     */
    public static function emptyStorage(): array
    {
        return [
            'content' => null,
        ];
    }

    /**
     * @param  array<string, mixed>|null  $data
     * @return array{content: string}
     */
    public static function forForm(?array $data): array
    {
        $data = is_array($data) ? $data : [];

        if (static::isLegacyFormat($data)) {
            $data = static::migrateLegacy($data);
        }

        return [
            'content' => static::contentToHtml($data['content'] ?? null),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array{content: string|null}
     */
    public static function forStorage(array $data): array
    {
        $html = trim((string) ($data['content'] ?? ''));

        if (blank($html)) {
            return static::emptyStorage();
        }

        $html = static::normalizeHtml($html);

        if (blank(trim(strip_tags($html)))) {
            return static::emptyStorage();
        }

        return [
            'content' => $html,
        ];
    }

    /**
     * @param  array<string, mixed>|null  $data
     * @return array{html: string}
     */
    public static function forFrontend(?array $data): array
    {
        $data = is_array($data) ? $data : [];

        if (static::isLegacyFormat($data)) {
            $data = static::migrateLegacy($data);
        }

        $html = static::contentToHtml($data['content'] ?? null);

        if (blank(trim(strip_tags($html)))) {
            return ['html' => ''];
        }

        return ['html' => $html];
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private static function isLegacyFormat(array $data): bool
    {
        return ! array_key_exists('content', $data)
            && (array_key_exists('title', $data)
                || array_key_exists('body', $data)
                || array_key_exists('cta_text', $data));
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array{content: string|null}
     */
    private static function migrateLegacy(array $data): array
    {
        $title = trim((string) ($data['title'] ?? ''));
        $body = trim((string) ($data['body'] ?? ''));

        if ($title === '' && $body === '') {
            return static::emptyStorage();
        }

        if ($title !== '') {
            $html = sprintf(
                '<h2 class="max-w-prose text-display-xl lg:text-display-2xl" style="text-align: center">%s</h2>',
                e($title),
            );
        } else {
            $html = sprintf(
                '<p class="text-display-xl lg:text-display-2xl" style="text-align: center">%s</p>',
                e($body),
            );
        }

        return [
            'content' => static::normalizeHtml($html),
        ];
    }

    private static function normalizeHtml(string $html): string
    {
        return RichContent::toHtml(
            RichContent::normalizeDocument(
                RichContent::toDocument($html),
            ),
        );
    }

    private static function contentToHtml(mixed $content): string
    {
        if (blank($content)) {
            return '';
        }

        if (is_string($content)) {
            return trim($content);
        }

        if (is_array($content) && ($content['type'] ?? null) === 'doc') {
            return RichContent::toHtml(
                RichContent::normalizeDocument($content),
            );
        }

        return trim((string) $content);
    }
}
