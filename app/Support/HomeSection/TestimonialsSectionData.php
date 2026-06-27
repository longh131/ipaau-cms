<?php

namespace App\Support\HomeSection;

use App\Support\MediaUrl;

class TestimonialsSectionData
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
     * @return array{items: array<int, array{title: string, content: string, image: string}>}
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
     * @return array{items: array<int, array{title: string, content: string, image: string}>}
     */
    public static function forStorage(array $data): array
    {
        $items = [];

        foreach ($data['items'] ?? [] as $item) {
            if (! is_array($item)) {
                continue;
            }

            $normalized = static::normalizeItem($item);

            if ($normalized['title'] === '' && $normalized['content'] === '') {
                continue;
            }

            $items[] = $normalized;
        }

        return ['items' => $items];
    }

    /**
     * @param  array<string, mixed>|null  $data
     * @return array{items: array<int, array{title: string, title_lines: array<int, string>, content: string, image: ?string}>}
     */
    public static function forFrontend(?array $data): array
    {
        $form = static::forForm($data);

        return [
            'items' => collect($form['items'])
                ->filter(fn (array $item) => filled($item['title']) || filled($item['content']))
                ->map(fn (array $item) => [
                    'title' => $item['title'],
                    'title_lines' => static::titleLines($item['title']),
                    'content' => $item['content'],
                    'image' => MediaUrl::resolve($item['image']),
                ])
                ->values()
                ->all(),
        ];
    }

    /**
     * @param  array<string, mixed>  $item
     * @return array{title: string, content: string, image: string}
     */
    private static function normalizeItem(array $item): array
    {
        $title = trim((string) ($item['title'] ?? $item['name'] ?? $item['job_title'] ?? ''));
        $content = trim((string) ($item['content'] ?? $item['body'] ?? $item['quote'] ?? ''));

        return [
            'title' => $title,
            'content' => $content,
            'image' => MediaUrl::normalizeStoredPath($item['image'] ?? ''),
        ];
    }

    /**
     * @return array<int, string>
     */
    private static function titleLines(string $title): array
    {
        if ($title === '') {
            return [];
        }

        return collect(preg_split('/\R/', $title) ?: [])
            ->map(fn (string $line) => trim($line))
            ->filter(fn (string $line) => $line !== '')
            ->values()
            ->all();
    }
}
