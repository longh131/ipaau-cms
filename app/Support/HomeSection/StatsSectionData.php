<?php

namespace App\Support\HomeSection;

class StatsSectionData
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
     * @return array{items: array<int, array{number: string, title: string, content: string}>}
     */
    public static function forForm(?array $data): array
    {
        $data = is_array($data) ? $data : [];
        $items = [];

        foreach ($data['items'] ?? [] as $item) {
            if (! is_array($item)) {
                continue;
            }

            $items[] = static::normalizeItem($item);
        }

        return ['items' => $items];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array{items: array<int, array{number: string, title: string, content: string}>}
     */
    public static function forStorage(array $data): array
    {
        $items = [];

        foreach ($data['items'] ?? [] as $item) {
            if (! is_array($item)) {
                continue;
            }

            $normalized = static::normalizeItem($item);

            if ($normalized['number'] === '') {
                continue;
            }

            $items[] = $normalized;
        }

        return ['items' => $items];
    }

    /**
     * @param  array<string, mixed>|null  $data
     * @return array{items: array<int, array{number: string, title: string, content: string}>}
     */
    public static function forFrontend(?array $data): array
    {
        $form = static::forForm($data);

        return [
            'items' => collect($form['items'])
                ->filter(fn (array $item) => filled($item['number']))
                ->values()
                ->all(),
        ];
    }

    /**
     * @param  array<string, mixed>  $item
     * @return array{number: string, title: string, content: string}
     */
    private static function normalizeItem(array $item): array
    {
        $number = trim((string) ($item['number'] ?? $item['value'] ?? ''));
        $title = trim((string) ($item['title'] ?? $item['label'] ?? ''));
        $content = trim((string) ($item['content'] ?? $item['description'] ?? $item['body'] ?? ''));

        return [
            'number' => $number,
            'title' => $title,
            'content' => $content,
        ];
    }
}
