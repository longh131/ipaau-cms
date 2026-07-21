<?php

namespace App\Support\PageTemplate\Templates;

use App\Models\Page;
use App\Support\MediaUrl;
use App\Support\PageTemplate\PageBodyBlocks;

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
            'bento_style' => self::BENTO_STYLE_FIVE,
            'bento_cards' => [],
            'content_title' => '',
            'content_title_align' => 'left',
            'content_body' => '',
            'content_button_label' => '',
            'content_button_url' => '',
            'content_button_target' => '',
            'card_list_title' => '',
            'card_list_items' => [],
        ];
    }

    /**
     * @param  array<string, mixed>|null  $data
     * @return array<string, mixed>
     */
    public static function forForm(?array $data): array
    {
        $data = self::resolveStoredData($data);
        $content = self::normalizeContentBlock($data);

        return [
            'heading' => trim((string) ($data['heading'] ?? '')),
            'summary' => trim((string) ($data['summary'] ?? '')),
            'bento_style' => self::normalizeBentoStyle((string) ($data['bento_style'] ?? self::BENTO_STYLE_FIVE)),
            'bento_cards' => self::normalizeBentoCards(self::value($data, 'bento_cards') ?? []),
            'content_title' => $content['title'],
            'content_title_align' => $content['title_align'],
            'content_body' => $content['body'],
            'content_button_label' => $content['button_label'],
            'content_button_url' => $content['button_url'],
            'content_button_target' => $content['button_target'],
            'card_list_title' => trim((string) (self::value($data, 'card_list_title') ?? '')),
            'card_list_items' => self::normalizeCardListItems(self::value($data, 'card_list_items') ?? []),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public static function forStorage(array $data): array
    {
        return static::forForm($data);
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

        $contentBody = trim($form['content_body']);
        if ($contentBody === '' && filled(strip_tags((string) $page->content))) {
            $contentBody = trim((string) $page->content);
        }

        $contentBlock = [
            'title' => $form['content_title'],
            'title_align' => $form['content_title_align'],
            'content_html' => $contentBody,
            'button' => self::normalizeOptionalLink(
                $form['content_button_label'],
                $form['content_button_url'],
                $form['content_button_target'],
            ),
        ];

        $bentoCards = collect($form['bento_cards'])
            ->map(fn (array $card): array => [
                'title' => $card['title'],
                'image' => MediaUrl::resolve($card['image']),
                'url' => $card['url'],
                'target' => $card['target'],
            ])
            ->values()
            ->all();

        $cardListItems = collect($form['card_list_items'])
            ->map(fn (array $item): array => [
                'title' => $item['title'],
                'url' => $item['url'],
                'target' => $item['target'],
            ])
            ->values()
            ->all();

        return [
            'heading' => $heading,
            'summary' => $form['summary'],
            'bento_style' => $form['bento_style'],
            'bento_cards' => $bentoCards,
            'content_block' => $contentBlock,
            'card_list_title' => $form['card_list_title'],
            'card_list_items' => $cardListItems,
            'has_content' => static::hasContent($form, $page),
            'has_bento' => $bentoCards !== [],
            'has_content_block' => static::contentBlockHasContent($contentBlock),
            'has_card_list' => filled($form['card_list_title']) || $cardListItems !== [],
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

        if ($data['bento_cards'] !== []) {
            return true;
        }

        if (static::flatContentBlockHasContent(self::normalizeContentBlock($data))) {
            return true;
        }

        if (filled($data['card_list_title']) || $data['card_list_items'] !== []) {
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

        if (filled($form['content_title'])) {
            $parts[] = '<h2>'.e($form['content_title']).'</h2>';
        }

        if (filled(strip_tags($form['content_body']))) {
            $parts[] = $form['content_body'];
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
