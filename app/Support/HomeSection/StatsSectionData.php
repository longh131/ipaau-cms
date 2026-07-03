<?php

namespace App\Support\HomeSection;

use App\Support\MediaUrl;

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
     * @return array{items: array<int, array{number_type: string, number: string, number_image: string, title: string, content: string}>}
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
     * @return array{items: array<int, array{number_type: string, number: string, number_image: string, title: string, content: string}>}
     */
    public static function forStorage(array $data): array
    {
        $items = [];

        foreach ($data['items'] ?? [] as $item) {
            if (! is_array($item)) {
                continue;
            }

            $normalized = static::normalizeItem($item);

            if (! static::hasDisplayValue($normalized)) {
                continue;
            }

            $items[] = $normalized;
        }

        return ['items' => $items];
    }

    /**
     * @param  array<string, mixed>|null  $data
     * @return array{items: array<int, array{number_type: string, number: string, number_image: ?string, title: string, content: string}>}
     */
    public static function forFrontend(?array $data): array
    {
        $form = static::forForm($data);

        return [
            'items' => collect($form['items'])
                ->filter(fn (array $item) => static::hasDisplayValue($item))
                ->map(fn (array $item) => [
                    'number_type' => $item['number_type'],
                    'number' => $item['number'],
                    'number_image' => $item['number_type'] === 'image'
                        ? MediaUrl::resolve($item['number_image'])
                        : null,
                    'title' => $item['title'],
                    'content' => $item['content'],
                ])
                ->values()
                ->all(),
        ];
    }

    /**
     * @param  array{number_type: string, number: string, number_image: string}  $item
     */
    private static function hasDisplayValue(array $item): bool
    {
        if (($item['number_type'] ?? 'text') === 'image') {
            return filled($item['number_image'] ?? null);
        }

        return filled($item['number'] ?? null);
    }

    /**
     * @param  array<string, mixed>  $item
     * @return array{number_type: string, number: string, number_image: string, title: string, content: string}
     */
    private static function normalizeItem(array $item): array
    {
        $number = trim((string) ($item['number'] ?? $item['value'] ?? ''));
        $numberImage = MediaUrl::normalizeStoredPath($item['number_image'] ?? '');
        $numberType = (string) ($item['number_type'] ?? 'text');

        if (! in_array($numberType, ['text', 'image'], true)) {
            $numberType = 'text';
        }

        if ($numberType === 'text' && $number === '' && $numberImage !== '') {
            $numberType = 'image';
        }

        if ($numberType === 'image' && $numberImage === '' && $number !== '') {
            $numberType = 'text';
        }

        $title = trim((string) ($item['title'] ?? $item['label'] ?? ''));
        $content = trim((string) ($item['content'] ?? $item['description'] ?? $item['body'] ?? ''));

        return [
            'number_type' => $numberType,
            'number' => $numberType === 'text' ? $number : '',
            'number_image' => $numberType === 'image' ? $numberImage : '',
            'title' => $title,
            'content' => $content,
        ];
    }
}
