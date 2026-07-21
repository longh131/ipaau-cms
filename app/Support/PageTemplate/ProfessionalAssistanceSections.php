<?php

namespace App\Support\PageTemplate;

use App\Support\PageTemplate\Templates\BasicContentPageData;
use App\Support\RichContent;

class ProfessionalAssistanceSections
{
    public const TYPE_RICH_TEXT = PageBodyBlocks::TYPE_RICH_TEXT;

    public const TYPE_HTML_BODY = PageBodyBlocks::TYPE_HTML_BODY;

    public const TYPE_NEWS_LIST_A = GeneralSecondarySections::TYPE_NEWS_LIST_A;

    public const TYPE_MEDIA_SPLIT = PageBodyBlocks::TYPE_MEDIA_SPLIT;

    public const TYPE_CAROUSEL = PageBodyBlocks::TYPE_CAROUSEL;

    /** @var array<string, string> */
    public const TYPE_OPTIONS = [
        self::TYPE_RICH_TEXT => '富文本段落',
        self::TYPE_HTML_BODY => '正文（HTML 源码）',
        self::TYPE_NEWS_LIST_A => '新闻列表A',
        self::TYPE_MEDIA_SPLIT => '图文分栏',
        self::TYPE_CAROUSEL => '轮播',
    ];

    /**
     * @param  array<int, mixed>|null  $sections
     * @return array<int, array<string, mixed>>
     */
    public static function forForm(?array $sections): array
    {
        if (! is_array($sections)) {
            return [];
        }

        $normalized = [];

        foreach ($sections as $section) {
            if (! is_array($section)) {
                continue;
            }

            $type = (string) ($section['type'] ?? '');

            $normalizedSection = match ($type) {
                self::TYPE_RICH_TEXT,
                self::TYPE_MEDIA_SPLIT,
                self::TYPE_CAROUSEL => collect(PageBodyBlocks::forForm([$section]))->first(),
                self::TYPE_HTML_BODY => self::normalizeHtmlBodyForForm($section),
                self::TYPE_NEWS_LIST_A => self::normalizeNewsListAForForm($section),
                default => null,
            };

            if ($normalizedSection !== null) {
                $normalized[] = $normalizedSection;
            }
        }

        return $normalized;
    }

    /**
     * @param  array<int, mixed>|null  $sections
     * @return array<int, array<string, mixed>>
     */
    public static function forStorage(?array $sections): array
    {
        $sections = self::forForm($sections);

        $sections = array_map(function (array $section): array {
            if (in_array($section['type'], [self::TYPE_RICH_TEXT, self::TYPE_MEDIA_SPLIT, self::TYPE_CAROUSEL], true)) {
                return collect(PageBodyBlocks::forStorage([$section]))->first() ?? $section;
            }

            if ($section['type'] === self::TYPE_HTML_BODY) {
                $section['body'] = trim((string) ($section['body'] ?? ''));
            }

            if ($section['type'] === self::TYPE_NEWS_LIST_A) {
                $section['items'] = self::normalizeNewsListItems($section['items'] ?? []);
            }

            return $section;
        }, $sections);

        return array_values(array_filter(
            $sections,
            fn (array $section): bool => self::sectionHasContent($section),
        ));
    }

    /**
     * @param  array<int, mixed>|null  $sections
     * @return array<int, array<string, mixed>>
     */
    public static function forFrontend(?array $sections): array
    {
        $sections = self::forStorage($sections);
        $rendered = [];

        foreach ($sections as $section) {
            $renderedSection = match ($section['type']) {
                self::TYPE_RICH_TEXT,
                self::TYPE_MEDIA_SPLIT,
                self::TYPE_CAROUSEL => collect(PageBodyBlocks::forFrontend([$section]))->first(),
                self::TYPE_HTML_BODY => [
                    'type' => self::TYPE_HTML_BODY,
                    'tagline' => $section['tagline'],
                    'title' => $section['title'],
                    'body_html' => trim((string) ($section['body'] ?? '')),
                ],
                self::TYPE_NEWS_LIST_A => [
                    'type' => self::TYPE_NEWS_LIST_A,
                    'section_title' => $section['section_title'],
                    'summary_html' => RichContent::toHtml($section['summary'] ?? ''),
                    'view_more_label' => $section['view_more_label'],
                    'section_background' => GeneralSecondarySections::normalizeNewsListBackground(
                        (string) ($section['section_background'] ?? ''),
                    ),
                    'initial_visible' => GeneralSecondarySections::NEWS_LIST_INITIAL_VISIBLE,
                    'items' => self::normalizeNewsListItems($section['items'] ?? []),
                ],
                default => null,
            };

            if ($renderedSection !== null) {
                $rendered[] = $renderedSection;
            }
        }

        return $rendered;
    }

    /**
     * @param  array<int, mixed>|null  $sections
     */
    public static function hasContent(?array $sections): bool
    {
        return self::forStorage($sections) !== [];
    }

    /**
     * @param  array<string, mixed>  $section
     */
    protected static function sectionHasContent(array $section): bool
    {
        return match ($section['type']) {
            self::TYPE_RICH_TEXT,
            self::TYPE_MEDIA_SPLIT,
            self::TYPE_CAROUSEL => PageBodyBlocks::blockHasContent($section),
            self::TYPE_HTML_BODY => filled($section['tagline'] ?? null)
                || filled($section['title'] ?? null)
                || RichContent::hasVisibleHtml((string) ($section['body'] ?? '')),
            self::TYPE_NEWS_LIST_A => filled($section['section_title'] ?? null)
                || RichContent::hasVisibleHtml(RichContent::toHtml($section['summary'] ?? ''))
                || ($section['items'] ?? []) !== [],
            default => false,
        };
    }

    /**
     * @param  array<string, mixed>  $section
     * @return array<string, mixed>
     */
    protected static function normalizeHtmlBodyForForm(array $section): array
    {
        return [
            'type' => self::TYPE_HTML_BODY,
            'tagline' => trim((string) ($section['tagline'] ?? '')),
            'title' => trim((string) ($section['title'] ?? '')),
            'body' => BasicContentPageData::normalizeBodyForForm($section['body'] ?? ''),
        ];
    }

    /**
     * @param  array<string, mixed>  $section
     * @return array<string, mixed>
     */
    protected static function normalizeNewsListAForForm(array $section): array
    {
        $viewMoreLabel = trim((string) ($section['view_more_label'] ?? ''));

        return [
            'type' => self::TYPE_NEWS_LIST_A,
            'section_title' => trim((string) ($section['section_title'] ?? '')),
            'summary' => RichContent::encodeDocumentForForm($section['summary'] ?? ''),
            'view_more_label' => filled($viewMoreLabel) ? $viewMoreLabel : '查看更多',
            'section_background' => GeneralSecondarySections::normalizeNewsListBackground(
                (string) ($section['section_background'] ?? ''),
            ),
            'items' => self::normalizeNewsListItemsForForm($section['items'] ?? []),
        ];
    }

    /**
     * @return array<int, array{
     *     tagline: string,
     *     title: string,
     *     summary: string,
     *     url: string,
     *     target: string
     * }>
     */
    protected static function normalizeNewsListItems(mixed $items): array
    {
        if (! is_array($items)) {
            return [];
        }

        $normalized = [];

        foreach ($items as $item) {
            if (! is_array($item)) {
                continue;
            }

            $title = trim((string) ($item['title'] ?? ''));

            if ($title === '') {
                continue;
            }

            $tagline = trim((string) ($item['tagline'] ?? ''));
            $summary = trim((string) ($item['summary'] ?? ''));
            $url = trim((string) ($item['url'] ?? ''));
            $target = (string) ($item['target'] ?? '');
            $target = $target === '_blank' ? '_blank' : '';

            $normalized[] = [
                'tagline' => $tagline,
                'title' => $title,
                'summary' => $summary,
                'url' => $url,
                'target' => $target,
            ];
        }

        return $normalized;
    }

    /**
     * @return array<int, array{
     *     tagline: string,
     *     title: string,
     *     summary: string,
     *     url: string,
     *     target: string
     * }>
     */
    protected static function normalizeNewsListItemsForForm(mixed $items): array
    {
        if (! is_array($items)) {
            return [];
        }

        $normalized = [];

        foreach ($items as $item) {
            if (! is_array($item)) {
                continue;
            }

            $title = trim((string) ($item['title'] ?? ''));
            $tagline = trim((string) ($item['tagline'] ?? ''));
            $summary = trim((string) ($item['summary'] ?? ''));
            $url = trim((string) ($item['url'] ?? ''));

            if ($title === '' && $tagline === '' && $summary === '' && $url === '') {
                continue;
            }

            $target = (string) ($item['target'] ?? '');
            $target = $target === '_blank' ? '_blank' : '';

            $normalized[] = [
                'tagline' => $tagline,
                'title' => $title,
                'summary' => $summary,
                'url' => $url,
                'target' => $target,
            ];
        }

        return $normalized;
    }
}
