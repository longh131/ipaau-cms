<?php

namespace App\Support\HomeSection;

use App\Support\MediaUrl;

class TabbedContentSectionData
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
     * @return array{tabs: array<int, array{tab_label: string, tagline: string, title: string, description: string, button_label: string, button_url: string, image: string}>}
     */
    public static function forForm(?array $data): array
    {
        $data = is_array($data) ? $data : [];
        $tabs = [];

        foreach ($data['tabs'] ?? [] as $tab) {
            if (! is_array($tab)) {
                continue;
            }

            $tabs[] = static::normalizeItem($tab);
        }

        return ['tabs' => $tabs];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array{tabs: array<int, array{tab_label: string, tagline: string, title: string, description: string, button_label: string, button_url: string, image: string}>}
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
     * @return array{tabs: array<int, array{tab_label: string, tagline: string, title: string, description: string, button_label: string, url: ?string, image: ?string}>}
     */
    public static function forFrontend(?array $data): array
    {
        $form = static::forForm($data);

        return [
            'tabs' => collect($form['tabs'])
                ->filter(fn (array $tab) => filled($tab['tab_label']))
                ->map(fn (array $tab) => [
                    'tab_label' => $tab['tab_label'],
                    'tagline' => $tab['tagline'],
                    'title' => $tab['title'],
                    'description' => $tab['description'],
                    'button_label' => $tab['button_label'],
                    'url' => filled($tab['button_url']) ? $tab['button_url'] : null,
                    'image' => MediaUrl::resolve($tab['image']),
                ])
                ->values()
                ->all(),
        ];
    }

    /**
     * @param  array<string, mixed>  $tab
     * @return array{tab_label: string, tagline: string, title: string, description: string, button_label: string, button_url: string, image: string}
     */
    private static function normalizeItem(array $tab): array
    {
        return [
            'tab_label' => trim((string) ($tab['tab_label'] ?? $tab['label'] ?? '')),
            'tagline' => trim((string) ($tab['tagline'] ?? $tab['eyebrow'] ?? '')),
            'title' => trim((string) ($tab['title'] ?? '')),
            'description' => trim((string) ($tab['description'] ?? $tab['body'] ?? '')),
            'button_label' => trim((string) ($tab['button_label'] ?? $tab['cta_text'] ?? '')),
            'button_url' => trim((string) ($tab['button_url'] ?? $tab['cta_url'] ?? $tab['url'] ?? '')),
            'image' => MediaUrl::normalizeStoredPath($tab['image'] ?? ''),
        ];
    }
}
