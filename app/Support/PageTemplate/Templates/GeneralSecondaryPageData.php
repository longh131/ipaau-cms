<?php

namespace App\Support\PageTemplate\Templates;

use App\Models\Page;
use App\Support\PageTemplate\GeneralSecondarySections;
use App\Support\RichContent;

class GeneralSecondaryPageData
{
    /**
     * @return array{heading: string, summary: string, sections: array<int, array<string, mixed>>}
     */
    public static function emptyStorage(): array
    {
        return [
            'heading' => '',
            'summary' => '',
            'sections' => [],
        ];
    }

    /**
     * @param  array<string, mixed>|null  $data
     * @return array{heading: string, summary: string, sections: array<int, array<string, mixed>>}
     */
    public static function forForm(?array $data): array
    {
        $data = is_array($data) ? $data : [];

        return [
            'heading' => trim((string) ($data['heading'] ?? '')),
            'summary' => trim((string) ($data['summary'] ?? '')),
            'sections' => GeneralSecondarySections::forForm($data['sections'] ?? []),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array{heading: string, summary: string, sections: array<int, array<string, mixed>>}
     */
    public static function forStorage(array $data): array
    {
        $form = static::forForm($data);

        return [
            'heading' => $form['heading'],
            'summary' => $form['summary'],
            'sections' => GeneralSecondarySections::forStorage($form['sections']),
        ];
    }

    /**
     * @param  array<string, mixed>|null  $data
     * @return array{
     *     heading: string,
     *     summary: string,
     *     sections: array<int, array<string, mixed>>,
     *     has_content: bool
     * }
     */
    public static function forFrontend(?array $data, Page $page): array
    {
        $form = static::forForm($data);
        $heading = filled($form['heading']) ? $form['heading'] : $page->displayTitle();

        return [
            'heading' => $heading,
            'summary' => $form['summary'],
            'sections' => GeneralSecondarySections::forFrontend($form['sections']),
            'has_content' => static::hasContent($form, $page),
        ];
    }

    /**
     * @param  array<string, mixed>|null  $data
     */
    public static function hasContent(?array $data, ?Page $page = null): bool
    {
        $data = static::forForm($data);

        if (filled($data['summary']) || filled($data['heading'])) {
            return true;
        }

        if (GeneralSecondarySections::hasContent($data['sections'])) {
            return true;
        }

        return filled($page?->displayTitle());
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public static function contentSnapshot(array $data): string
    {
        $form = static::forForm($data);
        $parts = [];

        if (filled($form['summary'])) {
            $parts[] = '<p>'.e($form['summary']).'</p>';
        }

        foreach ($form['sections'] as $section) {
            if (($section['type'] ?? '') === GeneralSecondarySections::TYPE_CONTENT_BLOCK) {
                if (filled($section['title'] ?? null)) {
                    $parts[] = '<h2>'.e($section['title']).'</h2>';
                }

                $html = RichContent::toHtml($section['content'] ?? '');

                if (filled(strip_tags($html))) {
                    $parts[] = $html;
                }
            }

            if (($section['type'] ?? '') === GeneralSecondarySections::TYPE_FAQ) {
                foreach ($section['items'] ?? [] as $item) {
                    if (filled($item['question'] ?? null)) {
                        $parts[] = '<h3>'.e($item['question']).'</h3>';
                    }

                    $answer = RichContent::toHtml($item['answer'] ?? '');

                    if (filled(strip_tags($answer))) {
                        $parts[] = $answer;
                    }
                }
            }

            if (($section['type'] ?? '') === GeneralSecondarySections::TYPE_NEWS_LIST) {
                if (filled($section['section_title'] ?? null)) {
                    $parts[] = '<h2>'.e($section['section_title']).'</h2>';
                }

                foreach ($section['items'] ?? [] as $item) {
                    if (filled($item['title'] ?? null)) {
                        $parts[] = '<h3>'.e($item['title']).'</h3>';
                    }

                    if (filled($item['summary'] ?? null)) {
                        $parts[] = '<p>'.e($item['summary']).'</p>';
                    }
                }
            }
        }

        return implode("\n", $parts);
    }
}
