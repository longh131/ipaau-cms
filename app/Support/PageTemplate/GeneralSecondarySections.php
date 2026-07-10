<?php

namespace App\Support\PageTemplate;

use App\Support\HomeSection\FaqSectionData;
use App\Support\RichContent;

class GeneralSecondarySections
{
    public const TYPE_CONTENT_BLOCK = 'content_block';

    public const TYPE_FAQ = 'faq';

    public const TYPE_NEWS_LIST = 'news_list';

    public const NEWS_LIST_INITIAL_VISIBLE = 3;

    /** @var array<string, string> */
    public const TYPE_OPTIONS = [
        self::TYPE_CONTENT_BLOCK => '富文本模块',
        self::TYPE_FAQ => '手风琴 FAQ',
        self::TYPE_NEWS_LIST => '新闻列表',
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
                self::TYPE_CONTENT_BLOCK => self::normalizeContentBlockForForm($section),
                self::TYPE_FAQ => self::normalizeFaqForForm($section),
                self::TYPE_NEWS_LIST => self::normalizeNewsListForForm($section),
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
            if ($section['type'] === self::TYPE_CONTENT_BLOCK) {
                $section['buttons'] = self::normalizeButtons($section['buttons'] ?? []);
            }

            if ($section['type'] === self::TYPE_FAQ) {
                $section['items'] = FaqSectionData::forStorage(['items' => $section['items']])['items'];
            }

            if ($section['type'] === self::TYPE_NEWS_LIST) {
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
                self::TYPE_CONTENT_BLOCK => [
                    'type' => self::TYPE_CONTENT_BLOCK,
                    'title' => $section['title'],
                    'title_align' => $section['title_align'],
                    'content_html' => RichContent::toHtml($section['content']),
                    'buttons' => $section['buttons'],
                ],
                self::TYPE_FAQ => [
                    'type' => self::TYPE_FAQ,
                    'tagline' => $section['tagline'],
                    'title' => $section['title'],
                    'intro' => $section['intro'],
                    'items' => FaqSectionData::forFrontend(['items' => $section['items']])['items'],
                ],
                self::TYPE_NEWS_LIST => [
                    'type' => self::TYPE_NEWS_LIST,
                    'section_title' => $section['section_title'],
                    'summary_html' => RichContent::toHtml($section['summary'] ?? ''),
                    'view_more_label' => $section['view_more_label'],
                    'initial_visible' => self::NEWS_LIST_INITIAL_VISIBLE,
                    'items' => $section['items'],
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
     * @return array<string, mixed>
     */
    protected static function normalizeContentBlockForForm(array $section): array
    {
        return [
            'type' => self::TYPE_CONTENT_BLOCK,
            'title' => trim((string) ($section['title'] ?? '')),
            'title_align' => self::normalizeTitleAlign((string) ($section['title_align'] ?? 'left')),
            'content' => $section['content'] ?? '',
            'buttons' => self::normalizeButtonsForForm($section['buttons'] ?? []),
        ];
    }

    /**
     * @param  array<string, mixed>  $section
     * @return array<string, mixed>
     */
    protected static function normalizeFaqForForm(array $section): array
    {
        return [
            'type' => self::TYPE_FAQ,
            'tagline' => trim((string) ($section['tagline'] ?? '')),
            'title' => trim((string) ($section['title'] ?? '')),
            'intro' => trim((string) ($section['intro'] ?? '')),
            'items' => FaqSectionData::forForm(['items' => $section['items'] ?? []])['items'],
        ];
    }

    /**
     * @param  array<string, mixed>  $section
     * @return array<string, mixed>
     */
    protected static function normalizeNewsListForForm(array $section): array
    {
        $viewMoreLabel = trim((string) ($section['view_more_label'] ?? ''));

        return [
            'type' => self::TYPE_NEWS_LIST,
            'section_title' => trim((string) ($section['section_title'] ?? '')),
            'summary' => $section['summary'] ?? '',
            'view_more_label' => filled($viewMoreLabel) ? $viewMoreLabel : '查看更多',
            'items' => self::normalizeNewsListItemsForForm($section['items'] ?? []),
        ];
    }

    /**
     * @return array<int, array{title: string, summary: string, url: string, target: string}>
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

            $summary = trim((string) ($item['summary'] ?? ''));
            $url = trim((string) ($item['url'] ?? ''));
            $target = (string) ($item['target'] ?? '');
            $target = $target === '_blank' ? '_blank' : '';

            $normalized[] = [
                'title' => $title,
                'summary' => $summary,
                'url' => $url,
                'target' => $target,
            ];
        }

        return $normalized;
    }

    /**
     * @return array<int, array{title: string, summary: string, url: string, target: string}>
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
            $summary = trim((string) ($item['summary'] ?? ''));
            $url = trim((string) ($item['url'] ?? ''));

            if ($title === '' && $summary === '' && $url === '') {
                continue;
            }

            $target = (string) ($item['target'] ?? '');
            $target = $target === '_blank' ? '_blank' : '';

            $normalized[] = [
                'title' => $title,
                'summary' => $summary,
                'url' => $url,
                'target' => $target,
            ];
        }

        return $normalized;
    }

    public static function normalizeTitleAlign(string $align): string
    {
        return array_key_exists($align, PageBodyBlocks::TITLE_ALIGN_OPTIONS)
            ? $align
            : 'left';
    }

    /**
     * @param  array<string, mixed>  $section
     */
    protected static function sectionHasContent(array $section): bool
    {
        return match ($section['type']) {
            self::TYPE_CONTENT_BLOCK => filled($section['title'] ?? null)
                || filled(strip_tags(RichContent::toHtml($section['content'] ?? '')))
                || ($section['buttons'] ?? []) !== [],
            self::TYPE_FAQ => ($section['items'] ?? []) !== [],
            self::TYPE_NEWS_LIST => filled($section['section_title'] ?? null)
                || filled(strip_tags(RichContent::toHtml($section['summary'] ?? '')))
                || ($section['items'] ?? []) !== [],
            default => false,
        };
    }

    /**
     * @return array<int, array{label: string, url: string, style: string, target: string}>
     */
    protected static function normalizeButtons(mixed $buttons): array
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
     * @return array<int, array{label: string, url: string, style: string, target: string}>
     */
    protected static function normalizeButtonsForForm(mixed $buttons): array
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

            if ($label === '' && $url === '') {
                continue;
            }

            $style = (string) ($button['style'] ?? 'primary');
            $style = in_array($style, ['primary', 'secondary'], true) ? $style : 'primary';

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
}
