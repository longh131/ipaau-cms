<?php

namespace App\Support\HomeSection;

use App\Support\MediaUrl;
use App\Support\RichContent;

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
     * @return array{tabs: array<int, array<string, mixed>>}
     */
    public static function forForm(?array $data): array
    {
        $data = is_array($data) ? $data : [];
        $tabs = [];

        foreach ($data['tabs'] ?? [] as $tab) {
            if (! is_array($tab)) {
                continue;
            }

            $normalized = static::normalizeItem($tab, forForm: true);
            $normalized['description'] = RichContent::encodeDocumentForForm($normalized['description']);

            $tabs[] = $normalized;
        }

        return ['tabs' => $tabs];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array{tabs: array<int, array<string, mixed>>}
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
     * @return array{tabs: array<int, array<string, mixed>>}
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
                    'description_html' => RichContent::toHtml($tab['description']),
                    'buttons' => $tab['buttons'],
                    'image' => MediaUrl::resolve($tab['image']),
                ])
                ->values()
                ->all(),
        ];
    }

    /**
     * @param  array<string, mixed>  $tab
     * @return array<string, mixed>
     */
    private static function normalizeItem(array $tab, bool $forForm = false): array
    {
        $buttons = static::normalizeButtons($tab['buttons'] ?? []);

        if ($buttons === []) {
            $label = trim((string) ($tab['button_label'] ?? $tab['cta_text'] ?? ''));
            $url = trim((string) ($tab['button_url'] ?? $tab['cta_url'] ?? $tab['url'] ?? ''));

            if ($label !== '' && $url !== '') {
                $buttons[] = [
                    'label' => $label,
                    'url' => $url,
                    'style' => 'secondary',
                    'target' => '',
                ];
            }
        }

        if ($forForm) {
            $buttons = static::normalizeButtonsForForm($buttons);
        }

        return [
            'tab_label' => trim((string) ($tab['tab_label'] ?? $tab['label'] ?? '')),
            'tagline' => trim((string) ($tab['tagline'] ?? $tab['eyebrow'] ?? '')),
            'title' => trim((string) ($tab['title'] ?? '')),
            'description' => $tab['description'] ?? $tab['body'] ?? '',
            'buttons' => $buttons,
            'image' => MediaUrl::normalizeStoredPath($tab['image'] ?? ''),
        ];
    }

    /**
     * @return array<int, array{label: string, url: string, style: string, target: string}>
     */
    private static function normalizeButtons(mixed $buttons): array
    {
        if (! is_array($buttons)) {
            return [];
        }

        $normalized = [];

        foreach ($buttons as $button) {
            if (! is_array($button)) {
                continue;
            }

            $label = trim((string) ($button['label'] ?? ''));
            $url = trim((string) ($button['url'] ?? ''));

            if ($label === '' || $url === '') {
                continue;
            }

            $style = (string) ($button['style'] ?? 'secondary');
            $style = in_array($style, ['primary', 'secondary'], true) ? $style : 'secondary';

            $target = (string) ($button['target'] ?? '');
            $target = $target === '_blank' ? '_blank' : '';

            $normalized[] = [
                'label' => $label,
                'url' => $url,
                'style' => $style,
                'target' => $target,
            ];
        }

        return $normalized;
    }

    /**
     * @param  array<int, array{label: string, url: string, style: string, target: string}>  $buttons
     * @return array<int, array{label: string, url: string, style: string, target: string}>
     */
    private static function normalizeButtonsForForm(array $buttons): array
    {
        return array_map(static fn (array $button): array => [
            'label' => $button['label'],
            'url' => $button['url'],
            'style' => in_array($button['style'], ['primary', 'secondary'], true) ? $button['style'] : 'secondary',
            'target' => $button['target'] === '_blank' ? '_blank' : '',
        ], $buttons);
    }
}
