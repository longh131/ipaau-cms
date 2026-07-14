<?php

namespace App\Support\PageTemplate\Templates;

use App\Models\Page;
use App\Support\PageTemplate\GeneralSecondarySections;
use App\Support\RichContent;

class GeneralSecondaryPageData
{
    /**
     * @return array{
     *     heading: string,
     *     summary: string,
     *     buttons: array<int, array{label: string, url: string, style: string, target: string}>,
     *     sections: array<int, array<string, mixed>>
     * }
     */
    public static function emptyStorage(): array
    {
        return [
            'heading' => '',
            'summary' => '',
            'buttons' => [],
            'sections' => [],
        ];
    }

    /**
     * @param  array<string, mixed>|null  $data
     * @return array{
     *     heading: string,
     *     summary: string,
     *     buttons: array<int, array{label: string, url: string, style: string, target: string}>,
     *     sections: array<int, array<string, mixed>>
     * }
     */
    public static function forForm(?array $data): array
    {
        $data = is_array($data) ? $data : [];

        return [
            'heading' => trim((string) ($data['heading'] ?? '')),
            'summary' => RichContent::encodeDocumentForForm($data['summary'] ?? ''),
            'buttons' => GeneralSecondarySections::buttonsForForm($data['buttons'] ?? []),
            'sections' => GeneralSecondarySections::forForm($data['sections'] ?? []),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array{
     *     heading: string,
     *     summary: string,
     *     buttons: array<int, array{label: string, url: string, style: string, target: string}>,
     *     sections: array<int, array<string, mixed>>
     * }
     */
    public static function forStorage(array $data): array
    {
        $form = static::forForm($data);

        return [
            'heading' => $form['heading'],
            'summary' => $form['summary'],
            'buttons' => GeneralSecondarySections::buttonsForStorage($form['buttons']),
            'sections' => GeneralSecondarySections::forStorage($form['sections']),
        ];
    }

    /**
     * @param  array<string, mixed>|null  $data
     * @return array{
     *     heading: string,
     *     summary: string,
     *     buttons: array<int, array{label: string, url: string, style: string, target: string}>,
     *     sections: array<int, array<string, mixed>>,
     *     has_content: bool
     * }
     */
    public static function forFrontend(?array $data, Page $page): array
    {
        $form = static::forForm($data);
        $storage = static::forStorage($data ?? []);

        return [
            'heading' => $form['heading'],
            'summary_html' => RichContent::toHtml($form['summary'] ?? ''),
            'buttons' => $storage['buttons'],
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

        if (static::summaryHasContent($data['summary'] ?? null) || filled($data['heading'])) {
            return true;
        }

        if (GeneralSecondarySections::buttonsForStorage($data['buttons'] ?? []) !== []) {
            return true;
        }

        return GeneralSecondarySections::hasContent($data['sections']);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public static function contentSnapshot(array $data): string
    {
        $form = static::forForm($data);
        $parts = [];

        if (static::summaryHasContent($form['summary'] ?? null)) {
            $parts[] = RichContent::toHtml($form['summary']);
        }

        foreach ($form['sections'] as $section) {
            if (($section['type'] ?? '') === GeneralSecondarySections::TYPE_CONTENT_BLOCK) {
                if (filled($section['tagline'] ?? null)) {
                    $parts[] = '<p>'.e($section['tagline']).'</p>';
                }

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

            if (($section['type'] ?? '') === GeneralSecondarySections::TYPE_NEWS_LIST_A
                || ($section['type'] ?? '') === GeneralSecondarySections::TYPE_NEWS_LIST) {
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

            if (($section['type'] ?? '') === GeneralSecondarySections::TYPE_STATS) {
                foreach ($section['items'] ?? [] as $item) {
                    if (filled($item['title'] ?? null)) {
                        $parts[] = '<h3>'.e($item['title']).'</h3>';
                    }

                    if (filled($item['content'] ?? null)) {
                        $parts[] = '<p>'.e($item['content']).'</p>';
                    }
                }
            }

            if (($section['type'] ?? '') === GeneralSecondarySections::TYPE_TESTIMONIALS) {
                if (filled($section['section_title'] ?? null)) {
                    $parts[] = '<h2>'.e($section['section_title']).'</h2>';
                }

                foreach ($section['items'] ?? [] as $item) {
                    if (filled($item['title'] ?? null)) {
                        $parts[] = '<h3>'.e($item['title']).'</h3>';
                    }

                    if (filled($item['content'] ?? null)) {
                        $parts[] = '<p>'.e($item['content']).'</p>';
                    }
                }
            }

            if (($section['type'] ?? '') === GeneralSecondarySections::TYPE_NEWSLETTER) {
                if (filled($section['title'] ?? null)) {
                    $parts[] = '<h2>'.e($section['title']).'</h2>';
                }

                $newsletterHtml = RichContent::toHtml($section['content'] ?? '');

                if (filled(strip_tags($newsletterHtml))) {
                    $parts[] = $newsletterHtml;
                }
            }

            if (($section['type'] ?? '') === GeneralSecondarySections::TYPE_HTML_BODY) {
                if (filled(strip_tags((string) ($section['body'] ?? '')))) {
                    $parts[] = (string) $section['body'];
                }
            }

            if (($section['type'] ?? '') === GeneralSecondarySections::TYPE_LEFT_RIGHT_LAYOUT) {
                if (filled($section['tagline'] ?? null)) {
                    $parts[] = '<p>'.e($section['tagline']).'</p>';
                }

                if (filled($section['title'] ?? null)) {
                    $parts[] = '<h2>'.e($section['title']).'</h2>';
                }

                $html = RichContent::toHtml($section['content'] ?? '');

                if (filled(strip_tags($html))) {
                    $parts[] = $html;
                }
            }

            if (($section['type'] ?? '') === GeneralSecondarySections::TYPE_TABBED_CONTENT) {
                foreach ($section['tabs'] ?? [] as $tab) {
                    if (filled($tab['tab_label'] ?? null)) {
                        $parts[] = '<h3>'.e($tab['tab_label']).'</h3>';
                    }

                    if (filled($tab['title'] ?? null)) {
                        $parts[] = '<h2>'.e($tab['title']).'</h2>';
                    }

                    $html = RichContent::toHtml($tab['content'] ?? '');

                    if (RichContent::hasVisibleHtml($html)) {
                        $parts[] = $html;
                    }
                }
            }

            if (($section['type'] ?? '') === GeneralSecondarySections::TYPE_MEDIA_SPLIT) {
                if (filled($section['title'] ?? null)) {
                    $parts[] = '<h2>'.e($section['title']).'</h2>';
                }

                $html = RichContent::toHtml($section['content'] ?? '');

                if (RichContent::hasVisibleHtml($html)) {
                    $parts[] = $html;
                }
            }
        }

        return implode("\n", $parts);
    }

    public static function summaryHasContent(mixed $summary): bool
    {
        return RichContent::hasVisibleHtml(RichContent::toHtml($summary));
    }
}
