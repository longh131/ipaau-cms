<?php

namespace App\Support\PageTemplate;

use App\Support\RichContent;

class GeneralSecondaryTabbedContentSectionData
{
    /**
     * @return array{tabs: array<int, array<string, mixed>>}
     */
    public static function emptyStorage(): array
    {
        return [
            'tabs' => [],
        ];
    }

    /**
     * @param  array<string, mixed>|null  $data
     * @return array{tabs: array<int, array{
     *     tab_label: string,
     *     tagline: string,
     *     title: string,
     *     content: string,
     *     button_label: string,
     *     button_url: string
     * }>}
     */
    public static function forForm(?array $data): array
    {
        $data = is_array($data) ? $data : [];
        $tabs = [];

        foreach ($data['tabs'] ?? [] as $tab) {
            if (! is_array($tab)) {
                continue;
            }

            $normalized = static::normalizeItem($tab);
            $normalized['content'] = RichContent::encodeDocumentForForm($normalized['content']);

            $tabs[] = $normalized;
        }

        return ['tabs' => $tabs];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array{tabs: array<int, array{
     *     tab_label: string,
     *     tagline: string,
     *     title: string,
     *     content: string,
     *     button_label: string,
     *     button_url: string
     * }>}
     */
    public static function forStorage(array $data): array
    {
        $tabs = [];

        foreach ($data['tabs'] ?? [] as $tab) {
            if (! is_array($tab)) {
                continue;
            }

            $normalized = static::normalizeItem($tab);

            if ($normalized['tab_label'] === '') {
                continue;
            }

            $tabs[] = $normalized;
        }

        return ['tabs' => $tabs];
    }

    /**
     * @param  array<string, mixed>|null  $data
     * @return array{tabs: array<int, array{
     *     tab_label: string,
     *     tagline: string,
     *     title: string,
     *     content_html: string,
     *     button_label: string,
     *     url: ?string
     * }>}
     */
    public static function forFrontend(?array $data): array
    {
        $stored = static::forStorage(is_array($data) ? $data : []);

        return [
            'tabs' => collect($stored['tabs'])
                ->map(fn (array $tab): array => [
                    'tab_label' => $tab['tab_label'],
                    'tagline' => $tab['tagline'],
                    'title' => $tab['title'],
                    'content_html' => RichContent::toHtml($tab['content']),
                    'button_label' => $tab['button_label'],
                    'url' => filled($tab['button_url']) ? $tab['button_url'] : null,
                ])
                ->values()
                ->all(),
        ];
    }

    /**
     * @param  array<string, mixed>  $tab
     * @return array{
     *     tab_label: string,
     *     tagline: string,
     *     title: string,
     *     content: mixed,
     *     button_label: string,
     *     button_url: string
     * }
     */
    private static function normalizeItem(array $tab): array
    {
        return [
            'tab_label' => trim((string) ($tab['tab_label'] ?? $tab['label'] ?? '')),
            'tagline' => trim((string) ($tab['tagline'] ?? $tab['eyebrow'] ?? '')),
            'title' => trim((string) ($tab['title'] ?? '')),
            'content' => $tab['content'] ?? $tab['description'] ?? '',
            'button_label' => trim((string) ($tab['button_label'] ?? $tab['cta_text'] ?? '')),
            'button_url' => trim((string) ($tab['button_url'] ?? $tab['cta_url'] ?? $tab['url'] ?? '')),
        ];
    }
}
