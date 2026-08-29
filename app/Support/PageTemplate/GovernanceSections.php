<?php

namespace App\Support\PageTemplate;

use App\Support\MediaUrl;
use App\Support\PageTemplate\Templates\BasicContentPageData;
use App\Support\PageTemplate\Templates\GovernancePageData;
use App\Support\RichContent;

class GovernanceSections
{
    public const TYPE_BENTO = 'bento';

    /** @var array<string, string> */
    public const TYPE_OPTIONS = [
        self::TYPE_BENTO => 'Bento 导航卡片',
        GeneralSecondarySections::TYPE_CONTENT_BLOCK => '富文本模块',
        GeneralSecondarySections::TYPE_HTML_BODY => '正文（HTML 源码）',
        PageBodyBlocks::TYPE_CARD_LIST_CURATED => '精选卡片列表',
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
                self::TYPE_BENTO => self::normalizeBentoForForm($section),
                GeneralSecondarySections::TYPE_CONTENT_BLOCK => self::normalizeContentBlockForForm($section),
                GeneralSecondarySections::TYPE_HTML_BODY => self::normalizeHtmlBodyForForm($section),
                PageBodyBlocks::TYPE_CARD_LIST_CURATED => self::normalizeCardListForForm($section),
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
            if ($section['type'] === GeneralSecondarySections::TYPE_CONTENT_BLOCK) {
                $section['buttons'] = GeneralSecondarySections::buttonsForStorage($section['buttons'] ?? []);
            }

            if ($section['type'] === GeneralSecondarySections::TYPE_HTML_BODY) {
                $section['body'] = trim((string) ($section['body'] ?? ''));
            }

            if ($section['type'] === self::TYPE_BENTO) {
                $section['bento_style'] = GovernancePageData::normalizeBentoStyle((string) ($section['bento_style'] ?? ''));
                $section['cards'] = self::normalizeBentoCards($section['cards'] ?? []);
            }

            if ($section['type'] === PageBodyBlocks::TYPE_CARD_LIST_CURATED) {
                $section['section_title'] = trim((string) ($section['section_title'] ?? ''));
                $section['items'] = self::normalizeCardListItems($section['items'] ?? []);
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
                self::TYPE_BENTO => [
                    'type' => self::TYPE_BENTO,
                    'bento_style' => GovernancePageData::normalizeBentoStyle((string) ($section['bento_style'] ?? '')),
                    'bento_cards' => collect(self::normalizeBentoCards($section['cards'] ?? []))
                        ->map(fn (array $card): array => [
                            'title' => $card['title'],
                            'image' => MediaUrl::resolve($card['image']),
                            'url' => $card['url'],
                            'target' => $card['target'],
                        ])
                        ->values()
                        ->all(),
                ],
                GeneralSecondarySections::TYPE_CONTENT_BLOCK => [
                    'type' => GeneralSecondarySections::TYPE_CONTENT_BLOCK,
                    'tagline' => $section['tagline'],
                    'title' => $section['title'],
                    'title_align' => $section['title_align'],
                    'content_html' => RichContent::toHtml($section['content']),
                    'buttons' => $section['buttons'],
                ],
                GeneralSecondarySections::TYPE_HTML_BODY => [
                    'type' => GeneralSecondarySections::TYPE_HTML_BODY,
                    'body_html' => trim((string) ($section['body'] ?? '')),
                ],
                PageBodyBlocks::TYPE_CARD_LIST_CURATED => [
                    'type' => PageBodyBlocks::TYPE_CARD_LIST_CURATED,
                    'section_title' => $section['section_title'],
                    'items' => self::normalizeCardListItems($section['items'] ?? []),
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
    protected static function normalizeBentoForForm(array $section): array
    {
        $cards = $section['cards'] ?? $section['bento_cards'] ?? [];

        return [
            'type' => self::TYPE_BENTO,
            'bento_style' => GovernancePageData::normalizeBentoStyle((string) ($section['bento_style'] ?? GovernancePageData::BENTO_STYLE_FIVE)),
            'cards' => self::normalizeBentoCards($cards),
        ];
    }

    /**
     * @param  array<string, mixed>  $section
     * @return array<string, mixed>
     */
    protected static function normalizeContentBlockForForm(array $section): array
    {
        $normalized = GeneralSecondarySections::forForm([$section]);

        return $normalized[0] ?? [
            'type' => GeneralSecondarySections::TYPE_CONTENT_BLOCK,
            'tagline' => '',
            'title' => '',
            'title_align' => 'left',
            'content' => '',
            'buttons' => [],
        ];
    }

    /**
     * @param  array<string, mixed>  $section
     * @return array<string, mixed>
     */
    protected static function normalizeHtmlBodyForForm(array $section): array
    {
        return [
            'type' => GeneralSecondarySections::TYPE_HTML_BODY,
            'body' => BasicContentPageData::normalizeBodyForForm($section['body'] ?? ''),
        ];
    }

    /**
     * @param  array<string, mixed>  $section
     * @return array<string, mixed>
     */
    protected static function normalizeCardListForForm(array $section): array
    {
        $items = $section['items'] ?? $section['card_list_items'] ?? [];

        return [
            'type' => PageBodyBlocks::TYPE_CARD_LIST_CURATED,
            'section_title' => trim((string) ($section['section_title'] ?? $section['card_list_title'] ?? '')),
            'items' => self::normalizeCardListItems($items),
        ];
    }

    /**
     * @param  array<string, mixed>  $section
     */
    protected static function sectionHasContent(array $section): bool
    {
        return match ($section['type']) {
            self::TYPE_BENTO => self::normalizeBentoCards($section['cards'] ?? []) !== [],
            GeneralSecondarySections::TYPE_CONTENT_BLOCK => filled($section['tagline'] ?? null)
                || filled($section['title'] ?? null)
                || filled(strip_tags(RichContent::toHtml($section['content'] ?? '')))
                || ($section['buttons'] ?? []) !== [],
            GeneralSecondarySections::TYPE_HTML_BODY => RichContent::hasVisibleHtml((string) ($section['body'] ?? '')),
            PageBodyBlocks::TYPE_CARD_LIST_CURATED => filled($section['section_title'] ?? null)
                || self::normalizeCardListItems($section['items'] ?? []) !== [],
            default => false,
        };
    }

    /**
     * @return array<int, array{title: string, image: string, url: string, target: string}>
     */
    public static function normalizeBentoCards(mixed $cards): array
    {
        if (! is_array($cards)) {
            return [];
        }

        $normalized = [];

        foreach ($cards as $card) {
            if (! is_array($card)) {
                continue;
            }

            $title = trim((string) ($card['title'] ?? ''));
            $url = trim((string) ($card['url'] ?? ''));
            $image = MediaUrl::normalizeStoredPath($card['image'] ?? '');

            if ($title === '' && $url === '' && $image === '') {
                continue;
            }

            $target = (string) ($card['target'] ?? '');
            $target = $target === '_blank' ? '_blank' : '';

            $normalized[] = [
                'title' => $title,
                'image' => $image,
                'url' => $url,
                'target' => $target,
            ];
        }

        return $normalized;
    }

    /**
     * @return array<int, array{title: string, url: string, target: string}>
     */
    public static function normalizeCardListItems(mixed $items): array
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
            $url = trim((string) ($item['url'] ?? ''));

            if ($title === '' && $url === '') {
                continue;
            }

            $target = (string) ($item['target'] ?? '');
            $target = $target === '_blank' ? '_blank' : '';

            $normalized[] = [
                'title' => $title,
                'url' => $url,
                'target' => $target,
            ];
        }

        return $normalized;
    }
}
