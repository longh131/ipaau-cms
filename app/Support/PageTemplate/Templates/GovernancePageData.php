<?php

namespace App\Support\PageTemplate\Templates;

use App\Models\Page;
use App\Support\PageTemplate\GeneralSecondarySections;
use App\Support\PageTemplate\GovernanceSections;
use App\Support\PageTemplate\PageBodyBlocks;
use App\Support\RichContent;

class GovernancePageData
{
    public const CARD_LIST_INITIAL_VISIBLE = 3;

    public const BENTO_STYLE_FIVE = 'five';

    public const BENTO_STYLE_TALL = 'tall';

    public const BENTO_STYLE_WIDE = 'wide';

    /** @var array<string, string> */
    public const BENTO_STYLE_OPTIONS = [
        self::BENTO_STYLE_FIVE => '5 卡布局（Governance 默认）',
        self::BENTO_STYLE_TALL => '4 卡高布局',
        self::BENTO_STYLE_WIDE => '4 卡宽布局',
    ];

    /**
     * @return array<string, mixed>
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
     * @return array<string, mixed>
     */
    public static function forForm(?array $data): array
    {
        $data = self::resolveStoredData($data);

        return [
            'heading' => trim((string) ($data['heading'] ?? '')),
            'summary' => trim((string) ($data['summary'] ?? '')),
            'sections' => self::resolveSectionsForForm($data),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<int, array<string, mixed>>
     */
    protected static function resolveSectionsForForm(array $data): array
    {
        $sections = GovernanceSections::forForm($data['sections'] ?? []);
        $hasBentoInSections = collect($sections)->contains(
            fn (array $section): bool => ($section['type'] ?? '') === GovernanceSections::TYPE_BENTO,
        );
        $hasCardListInSections = collect($sections)->contains(
            fn (array $section): bool => ($section['type'] ?? '') === PageBodyBlocks::TYPE_CARD_LIST_CURATED,
        );

        $legacyBentoCards = GovernanceSections::normalizeBentoCards(self::value($data, 'bento_cards') ?? []);
        $legacyCardListItems = GovernanceSections::normalizeCardListItems(self::value($data, 'card_list_items') ?? []);
        $legacyCardListTitle = trim((string) (self::value($data, 'card_list_title') ?? ''));
        $legacyContentSections = self::legacyContentBlockAsSections($data);

        $result = [];

        if ($legacyBentoCards !== [] && ! $hasBentoInSections) {
            $result[] = [
                'type' => GovernanceSections::TYPE_BENTO,
                'bento_style' => self::normalizeBentoStyle((string) ($data['bento_style'] ?? self::BENTO_STYLE_FIVE)),
                'cards' => $legacyBentoCards,
            ];
        }

        if ($sections !== []) {
            $result = array_merge($result, $sections);
        } elseif ($legacyContentSections !== []) {
            $result = array_merge($result, $legacyContentSections);
        }

        if (($legacyCardListTitle !== '' || $legacyCardListItems !== []) && ! $hasCardListInSections) {
            $result[] = [
                'type' => PageBodyBlocks::TYPE_CARD_LIST_CURATED,
                'section_title' => $legacyCardListTitle,
                'items' => $legacyCardListItems,
            ];
        }

        return $result;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<int, array<string, mixed>>
     */
    protected static function legacyContentBlockAsSections(array $data): array
    {
        $content = self::normalizeContentBlock($data);

        if (! self::flatContentBlockHasContent($content)) {
            return [];
        }

        $bodyParts = [];

        if (filled($content['title'])) {
            $align = match ($content['title_align']) {
                'center' => 'center',
                'right' => 'right',
                default => 'left',
            };
            $bodyParts[] = '<h2 style="text-align:'.$align.'">'.e($content['title']).'</h2>';
        }

        if (filled(strip_tags($content['body']))) {
            $bodyParts[] = $content['body'];
        }

        if (filled($content['button_label']) && filled($content['button_url'])) {
            $target = $content['button_target'] === '_blank'
                ? ' target="_blank" rel="noopener noreferrer"'
                : '';
            $bodyParts[] = '<p><a href="'.e($content['button_url']).'" class="cta secondary"'.$target.'>'
                .e($content['button_label']).'</a></p>';
        }

        return [[
            'type' => GeneralSecondarySections::TYPE_HTML_BODY,
            'body' => implode("\n", $bodyParts),
        ]];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public static function forStorage(array $data): array
    {
        $form = static::forForm($data);

        return [
            'heading' => $form['heading'],
            'summary' => $form['summary'],
            'sections' => GovernanceSections::forStorage($form['sections']),
        ];
    }

    /**
     * @param  array<string, mixed>|null  $data
     * @return array<string, mixed>
     */
    public static function forFrontend(?array $data, Page $page): array
    {
        $data = self::resolveStoredData($data);
        $form = static::forForm($data);
        $heading = filled($form['heading']) ? $form['heading'] : $page->displayTitle();

        $sections = GovernanceSections::forFrontend($form['sections']);

        return [
            'heading' => $heading,
            'summary' => $form['summary'],
            'sections' => $sections,
            'has_content' => static::hasContent($form, $page),
            'has_sections' => $sections !== [],
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

        if (GovernanceSections::hasContent($data['sections'] ?? [])) {
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

        foreach (GovernanceSections::forStorage($form['sections'] ?? []) as $section) {
            if ($section['type'] === GeneralSecondarySections::TYPE_CONTENT_BLOCK) {
                if (filled($section['title'] ?? null)) {
                    $parts[] = '<h2>'.e($section['title']).'</h2>';
                }

                $parts[] = RichContent::toHtml($section['content'] ?? '');
            }

            if ($section['type'] === GeneralSecondarySections::TYPE_HTML_BODY && filled(strip_tags((string) ($section['body'] ?? '')))) {
                $parts[] = (string) $section['body'];
            }

            if ($section['type'] === GovernanceSections::TYPE_BENTO) {
                foreach ($section['cards'] ?? [] as $card) {
                    if (filled($card['title'] ?? null)) {
                        $parts[] = '<h3>'.e($card['title']).'</h3>';
                    }
                }
            }

            if ($section['type'] === PageBodyBlocks::TYPE_CARD_LIST_CURATED) {
                if (filled($section['section_title'] ?? null)) {
                    $parts[] = '<h2>'.e($section['section_title']).'</h2>';
                }

                foreach ($section['items'] ?? [] as $item) {
                    if (filled($item['title'] ?? null)) {
                        $parts[] = '<p>'.e($item['title']).'</p>';
                    }
                }
            }
        }

        return implode("\n", $parts);
    }

    public static function normalizeBentoStyle(string $style): string
    {
        return array_key_exists($style, self::BENTO_STYLE_OPTIONS)
            ? $style
            : self::BENTO_STYLE_FIVE;
    }

    public static function normalizeTitleAlign(string $align): string
    {
        return array_key_exists($align, PageBodyBlocks::TITLE_ALIGN_OPTIONS)
            ? $align
            : 'left';
    }

    /**
     * @return array<int, array{title: string, image: string, url: string, target: string}>
     */
    protected static function normalizeBentoCards(mixed $cards): array
    {
        return GovernanceSections::normalizeBentoCards($cards);
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array{
     *     title: string,
     *     title_align: string,
     *     body: string,
     *     button_label: string,
     *     button_url: string,
     *     button_target: string
     * }
     */
    protected static function normalizeContentBlock(array $data): array
    {
        $flat = self::normalizeFlatContentBlock($data);

        if (self::flatContentBlockHasContent($flat)) {
            return $flat;
        }

        $legacy = self::legacyContentBlock($data);

        if (self::flatContentBlockHasContent($legacy)) {
            return $legacy;
        }

        return $flat;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array{
     *     title: string,
     *     title_align: string,
     *     body: string,
     *     button_label: string,
     *     button_url: string,
     *     button_target: string
     * }
     */
    protected static function normalizeFlatContentBlock(array $data): array
    {
        $buttonTarget = (string) (self::value($data, 'content_button_target') ?? '');
        $buttonTarget = $buttonTarget === '_blank' ? '_blank' : '';

        return [
            'title' => trim((string) (self::value($data, 'content_title') ?? '')),
            'title_align' => self::normalizeTitleAlign((string) (self::value($data, 'content_title_align') ?? 'left')),
            'body' => trim((string) (self::value($data, 'content_body') ?? '')),
            'button_label' => trim((string) (self::value($data, 'content_button_label') ?? '')),
            'button_url' => trim((string) (self::value($data, 'content_button_url') ?? '')),
            'button_target' => $buttonTarget,
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array{
     *     title: string,
     *     title_align: string,
     *     body: string,
     *     button_label: string,
     *     button_url: string,
     *     button_target: string
     * }
     */
    protected static function legacyContentBlock(array $data): array
    {
        $legacyColumn = self::firstLegacyColumn(self::value($data, 'columns') ?? []);

        return [
            'title' => $legacyColumn['title'],
            'title_align' => 'left',
            'body' => $legacyColumn['content'],
            'button_label' => $legacyColumn['button_label'],
            'button_url' => $legacyColumn['button_url'],
            'button_target' => $legacyColumn['button_target'],
        ];
    }

    /**
     * @param  array{
     *     title: string,
     *     title_align: string,
     *     body: string,
     *     button_label: string,
     *     button_url: string,
     *     button_target: string
     * }  $block
     */
    protected static function flatContentBlockHasContent(array $block): bool
    {
        return filled($block['title'])
            || filled(strip_tags($block['body']))
            || (filled($block['button_label']) && filled($block['button_url']));
    }

    /**
     * @param  array<string, mixed>|null  $data
     * @return array<string, mixed>
     */
    protected static function resolveStoredData(?array $data): array
    {
        if (! is_array($data)) {
            return [];
        }

        $resolved = $data;

        foreach ($data as $value) {
            if (! is_array($value)) {
                continue;
            }

            foreach ([
                'sections',
                'content_title',
                'content_title_align',
                'content_body',
                'content_button_label',
                'content_button_url',
                'content_button_target',
                'card_list_title',
                'card_list_items',
                'columns',
            ] as $key) {
                if (! array_key_exists($key, $resolved) && array_key_exists($key, $value)) {
                    $resolved[$key] = $value[$key];
                }
            }
        }

        return $resolved;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected static function value(array $data, string $key): mixed
    {
        if (array_key_exists($key, $data)) {
            return $data[$key];
        }

        foreach ($data as $value) {
            if (is_array($value) && array_key_exists($key, $value)) {
                return $value[$key];
            }
        }

        return null;
    }

    /**
     * @return array{
     *     title: string,
     *     content: string,
     *     button_label: string,
     *     button_url: string,
     *     button_target: string
     * }
     */
    protected static function firstLegacyColumn(mixed $columns): array
    {
        if (! is_array($columns)) {
            return self::emptyLegacyColumn();
        }

        foreach ($columns as $column) {
            if (! is_array($column)) {
                continue;
            }

            $normalized = [
                'title' => trim((string) ($column['title'] ?? '')),
                'content' => trim((string) ($column['content'] ?? '')),
                'button_label' => trim((string) ($column['button_label'] ?? '')),
                'button_url' => trim((string) ($column['button_url'] ?? '')),
                'button_target' => (string) ($column['button_target'] ?? '') === '_blank' ? '_blank' : '',
            ];

            if (filled($normalized['title'])
                || filled(strip_tags($normalized['content']))
                || (filled($normalized['button_label']) && filled($normalized['button_url']))
            ) {
                return $normalized;
            }
        }

        return self::emptyLegacyColumn();
    }

    /**
     * @return array{
     *     title: string,
     *     content: string,
     *     button_label: string,
     *     button_url: string,
     *     button_target: string
     * }
     */
    protected static function emptyLegacyColumn(): array
    {
        return [
            'title' => '',
            'content' => '',
            'button_label' => '',
            'button_url' => '',
            'button_target' => '',
        ];
    }

    /**
     * @return array<int, array{title: string, url: string, target: string}>
     */
    protected static function normalizeCardListItems(mixed $items): array
    {
        return GovernanceSections::normalizeCardListItems($items);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected static function rawContentBlockHasContent(array $data): bool
    {
        return self::flatContentBlockHasContent(self::normalizeContentBlock($data));
    }

    /**
     * @param  array{
     *     title: string,
     *     title_align: string,
     *     content_html: string,
     *     button: ?array{label: string, url: string, style: string, target: string}
     * }  $block
     */
    protected static function contentBlockHasContent(array $block): bool
    {
        return filled($block['title'] ?? null)
            || filled(strip_tags($block['content_html'] ?? ''))
            || filled($block['button'] ?? null);
    }

    /**
     * @return array{label: string, url: string, style: string, target: string}|null
     */
    protected static function normalizeOptionalLink(string $label, string $url, string $target): ?array
    {
        if ($label === '' || $url === '') {
            return null;
        }

        return [
            'label' => $label,
            'url' => $url,
            'style' => 'secondary',
            'target' => $target === '_blank' ? '_blank' : '',
        ];
    }
}
