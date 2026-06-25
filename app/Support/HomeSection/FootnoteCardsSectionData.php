<?php

namespace App\Support\HomeSection;

use App\Support\MediaUrl;

class FootnoteCardsSectionData
{
    /**
     * @return array{items: array<int, array<string, mixed>>}
     */
    public static function emptyStorage(): array
    {
        return [
            'items' => [],
        ];
    }

    /**
     * @param  array<string, mixed>|null  $data
     * @return array{items: array<int, array{title: string, url: string, image_desktop: string, image_mobile: string, show_arrow: bool}>}
     */
    public static function forForm(?array $data): array
    {
        $data = is_array($data) ? $data : [];
        $items = [];

        foreach ($data['items'] ?? [] as $item) {
            if (! is_array($item)) {
                continue;
            }

            $items[] = [
                'title' => trim((string) ($item['title'] ?? '')),
                'url' => trim((string) ($item['url'] ?? '')),
                'image_desktop' => MediaUrl::normalizeStoredPath($item['image_desktop'] ?? ''),
                'image_mobile' => MediaUrl::normalizeStoredPath($item['image_mobile'] ?? ''),
                'show_arrow' => (bool) ($item['show_arrow'] ?? false),
            ];
        }

        return ['items' => $items];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array{items: array<int, array{title: string, url: string, image_desktop: string, image_mobile: string, show_arrow: bool}>}
     */
    public static function forStorage(array $data): array
    {
        $items = [];

        foreach ($data['items'] ?? [] as $item) {
            if (! is_array($item)) {
                continue;
            }

            $title = trim((string) ($item['title'] ?? ''));

            if ($title === '') {
                continue;
            }

            $url = trim((string) ($item['url'] ?? ''));

            $items[] = [
                'title' => $title,
                'url' => $url,
                'image_desktop' => MediaUrl::normalizeStoredPath($item['image_desktop'] ?? ''),
                'image_mobile' => MediaUrl::normalizeStoredPath($item['image_mobile'] ?? ''),
                'show_arrow' => (bool) ($item['show_arrow'] ?? false),
            ];
        }

        return ['items' => $items];
    }

    /**
     * @param  array<string, mixed>|null  $data
     * @return array{items: array<int, array{title: string, url: ?string, image_desktop: ?string, image_mobile: ?string, show_arrow: bool}>}
     */
    public static function forFrontend(?array $data): array
    {
        $form = static::forForm($data);

        return [
            'items' => collect($form['items'])
                ->map(function (array $item): array {
                    $desktop = MediaUrl::resolve($item['image_desktop']);
                    $mobile = MediaUrl::resolve($item['image_mobile']) ?? $desktop;

                    return [
                        'title' => $item['title'],
                        'url' => filled($item['url']) ? $item['url'] : null,
                        'image_desktop' => $desktop,
                        'image_mobile' => $mobile,
                        'show_arrow' => (bool) $item['show_arrow'],
                    ];
                })
                ->values()
                ->all(),
        ];
    }
}
