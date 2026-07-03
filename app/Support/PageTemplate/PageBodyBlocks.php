<?php

namespace App\Support\PageTemplate;

use App\Support\HomeSection\FaqSectionData;
use App\Support\HomeSection\StatsSectionData;
use App\Support\HomeSection\TabbedContentSectionData;
use App\Support\MediaUrl;
use App\Support\RichContent;

class PageBodyBlocks
{
    public const TYPE_RICH_TEXT = 'rich_text';

    public const TYPE_HIGHLIGHT = 'highlight';

    public const TYPE_CTA_GROUP = 'cta_group';

    public const TYPE_TABS = 'tabs';

    public const TYPE_CAROUSEL = 'carousel';

    public const TYPE_MEDIA_SPLIT = 'media_split';

    public const TYPE_FAQ = 'faq';

    public const TYPE_STATS = 'stats';

    /** @var array<string, string> */
    public const TYPE_OPTIONS = [
        self::TYPE_RICH_TEXT => '富文本段落',
        self::TYPE_HIGHLIGHT => '渐变强调句',
        self::TYPE_CTA_GROUP => '按钮组',
        self::TYPE_TABS => '选项卡板块',
        self::TYPE_CAROUSEL => '轮播',
        self::TYPE_MEDIA_SPLIT => '图文分栏',
        self::TYPE_FAQ => '手风琴 FAQ',
        self::TYPE_STATS => '数字统计',
    ];

    /** @var array<string, string> */
    public const IMAGE_POSITION_OPTIONS = [
        'left' => '图片在左',
        'right' => '图片在右',
    ];

    /** @var array<string, string> */
    public const IMAGE_SHAPE_OPTIONS = [
        'acorn' => '橡果形（About 页默认）',
        'rectangle' => '圆角矩形',
    ];

    /** @var array<string, string> */
    public const GRADIENT_OPTIONS = [
        'purple-reverse' => '紫色渐变',
        'blue-reverse' => '蓝色渐变',
        'orange-reverse' => '橙色渐变',
        'pink-reverse' => '粉色渐变',
        'purple' => '紫色渐变（正向）',
        'blue' => '蓝色渐变（正向）',
    ];

    /**
     * @param  array<int, mixed>|null  $blocks
     * @return array<int, array<string, mixed>>
     */
    public static function forForm(?array $blocks): array
    {
        if (! is_array($blocks)) {
            return [];
        }

        $normalized = [];

        foreach ($blocks as $block) {
            if (! is_array($block)) {
                continue;
            }

            $type = (string) ($block['type'] ?? '');

            $normalizedBlock = match ($type) {
                self::TYPE_RICH_TEXT => [
                    'type' => self::TYPE_RICH_TEXT,
                    'title' => trim((string) ($block['title'] ?? '')),
                    'html' => (string) ($block['html'] ?? ''),
                ],
                self::TYPE_HIGHLIGHT => [
                    'type' => self::TYPE_HIGHLIGHT,
                    'text' => trim((string) ($block['text'] ?? '')),
                    'gradient' => self::normalizeGradient((string) ($block['gradient'] ?? 'purple-reverse')),
                ],
                self::TYPE_CTA_GROUP => [
                    'type' => self::TYPE_CTA_GROUP,
                    'buttons' => self::normalizeButtons($block['buttons'] ?? []),
                ],
                self::TYPE_TABS => [
                    'type' => self::TYPE_TABS,
                    'tabs' => TabbedContentSectionData::forForm(['tabs' => $block['tabs'] ?? []])['tabs'],
                ],
                self::TYPE_CAROUSEL => [
                    'type' => self::TYPE_CAROUSEL,
                    'heading' => trim((string) ($block['heading'] ?? '')),
                    'slides' => self::normalizeCarouselSlides($block['slides'] ?? []),
                ],
                self::TYPE_MEDIA_SPLIT => self::normalizeMediaSplitBlock($block),
                self::TYPE_FAQ => [
                    'type' => self::TYPE_FAQ,
                    'tagline' => trim((string) ($block['tagline'] ?? '')),
                    'title' => trim((string) ($block['title'] ?? '')),
                    'intro' => trim((string) ($block['intro'] ?? '')),
                    'items' => FaqSectionData::forForm(['items' => $block['items'] ?? []])['items'],
                ],
                self::TYPE_STATS => [
                    'type' => self::TYPE_STATS,
                    'items' => StatsSectionData::forForm(['items' => $block['items'] ?? []])['items'],
                ],
                default => null,
            };

            if ($normalizedBlock !== null) {
                $normalized[] = $normalizedBlock;
            }
        }

        return $normalized;
    }

    /**
     * @param  array<int, mixed>|null  $blocks
     * @return array<int, array<string, mixed>>
     */
    public static function forStorage(?array $blocks): array
    {
        $blocks = static::forForm($blocks);

        $blocks = array_map(function (array $block): array {
            if ($block['type'] === self::TYPE_TABS) {
                $block['tabs'] = TabbedContentSectionData::forStorage(['tabs' => $block['tabs']])['tabs'];
            }

            if ($block['type'] === self::TYPE_FAQ) {
                $block['items'] = FaqSectionData::forStorage(['items' => $block['items']])['items'];
            }

            if ($block['type'] === self::TYPE_STATS) {
                $block['items'] = StatsSectionData::forStorage(['items' => $block['items']])['items'];
            }

            return $block;
        }, $blocks);

        return array_values(array_filter(
            $blocks,
            fn (array $block): bool => static::blockHasContent($block),
        ));
    }

    /**
     * @param  array<int, mixed>|null  $blocks
     * @return array<int, array<string, mixed>>
     */
    public static function forFrontend(?array $blocks, ?string $legacyContent = null): array
    {
        $blocks = static::forStorage($blocks);

        if ($blocks === [] && filled($legacyContent)) {
            $blocks = [
                [
                    'type' => self::TYPE_RICH_TEXT,
                    'title' => '',
                    'html' => (string) $legacyContent,
                ],
            ];
        }

        $rendered = [];

        foreach ($blocks as $block) {
            $renderedBlock = match ($block['type']) {
                self::TYPE_RICH_TEXT => [
                    'type' => self::TYPE_RICH_TEXT,
                    'title' => $block['title'],
                    'html' => RichContent::toHtml($block['html']),
                ],
                self::TYPE_HIGHLIGHT => [
                    'type' => self::TYPE_HIGHLIGHT,
                    'text' => $block['text'],
                    'gradient_class' => static::gradientClass($block['gradient']),
                ],
                self::TYPE_CTA_GROUP => [
                    'type' => self::TYPE_CTA_GROUP,
                    'buttons' => $block['buttons'],
                ],
                self::TYPE_TABS => [
                    'type' => self::TYPE_TABS,
                    'tabs' => TabbedContentSectionData::forFrontend(['tabs' => $block['tabs']])['tabs'],
                ],
                self::TYPE_CAROUSEL => [
                    'type' => self::TYPE_CAROUSEL,
                    'heading' => $block['heading'],
                    'slides' => $block['slides'],
                ],
                self::TYPE_MEDIA_SPLIT => [
                    'type' => self::TYPE_MEDIA_SPLIT,
                    'image_position' => $block['image_position'],
                    'image_shape' => $block['image_shape'],
                    'image' => MediaUrl::resolve($block['image']),
                    'tagline' => $block['tagline'],
                    'title' => $block['title'],
                    'content_html' => RichContent::toHtml($block['content']),
                    'buttons' => $block['buttons'],
                ],
                self::TYPE_FAQ => [
                    'type' => self::TYPE_FAQ,
                    'tagline' => $block['tagline'],
                    'title' => $block['title'],
                    'intro' => $block['intro'],
                    'items' => FaqSectionData::forFrontend(['items' => $block['items']])['items'],
                ],
                self::TYPE_STATS => [
                    'type' => self::TYPE_STATS,
                    'items' => StatsSectionData::forFrontend(['items' => $block['items']])['items'],
                ],
                default => null,
            };

            if ($renderedBlock !== null && static::blockHasContent($block)) {
                $rendered[] = $renderedBlock;
            }
        }

        return $rendered;
    }

    /**
     * @param  array<int, mixed>|null  $blocks
     */
    public static function hasContent(?array $blocks, ?string $legacyContent = null): bool
    {
        if (static::forStorage($blocks) !== []) {
            return true;
        }

        return filled(strip_tags((string) $legacyContent));
    }

    /**
     * @param  array<int, mixed>|null  $blocks
     */
    public static function legacyContentSnapshot(?array $blocks): string
    {
        $parts = [];

        foreach (static::forStorage($blocks) as $block) {
            if ($block['type'] !== self::TYPE_RICH_TEXT) {
                continue;
            }

            $html = RichContent::toHtml($block['html']);

            if (filled(strip_tags($html))) {
                $parts[] = $html;
            }
        }

        return implode("\n", $parts);
    }

    /**
     * @param  array<int, mixed>|null  $blocks
     */
    public static function needsAboutPageScripts(?array $blocks): bool
    {
        foreach (static::forStorage($blocks) as $block) {
            if (($block['type'] ?? null) === self::TYPE_TABS) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param  array<string, mixed>  $block
     */
    public static function blockHasContent(array $block): bool
    {
        return match ($block['type'] ?? null) {
            self::TYPE_RICH_TEXT => filled($block['title'] ?? null)
                || filled(strip_tags(RichContent::toHtml($block['html'] ?? ''))),
            self::TYPE_HIGHLIGHT => filled($block['text'] ?? null),
            self::TYPE_CTA_GROUP => ($block['buttons'] ?? []) !== [],
            self::TYPE_TABS => ($block['tabs'] ?? []) !== [],
            self::TYPE_CAROUSEL => ($block['slides'] ?? []) !== [],
            self::TYPE_MEDIA_SPLIT => filled($block['image'] ?? null)
                || filled($block['tagline'] ?? null)
                || filled($block['title'] ?? null)
                || filled(strip_tags(RichContent::toHtml($block['content'] ?? '')))
                || ($block['buttons'] ?? []) !== [],
            self::TYPE_FAQ => ($block['items'] ?? []) !== [],
            self::TYPE_STATS => ($block['items'] ?? []) !== [],
            default => false,
        };
    }

    public static function gradientClass(string $gradient): string
    {
        $gradient = static::normalizeGradient($gradient);

        return 'text-gradient-'.$gradient;
    }

    public static function normalizeGradient(string $gradient): string
    {
        return array_key_exists($gradient, self::GRADIENT_OPTIONS)
            ? $gradient
            : 'purple-reverse';
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
     * @return array<int, array{quote: string, author: string}>
     */
    protected static function normalizeCarouselSlides(mixed $slides): array
    {
        if (! is_array($slides)) {
            return [];
        }

        $normalized = [];

        foreach ($slides as $slide) {
            if (! is_array($slide)) {
                continue;
            }

            $quote = trim((string) ($slide['quote'] ?? ''));
            $author = trim((string) ($slide['author'] ?? ''));

            if ($quote === '' && $author === '') {
                continue;
            }

            $normalized[] = [
                'quote' => $quote,
                'author' => $author,
            ];
        }

        return $normalized;
    }

    /**
     * @param  array<string, mixed>  $block
     * @return array{
     *     type: string,
     *     image_position: string,
     *     image_shape: string,
     *     image: string,
     *     tagline: string,
     *     title: string,
     *     content: string,
     *     buttons: array<int, array{label: string, url: string, style: string, target: string}>
     * }
     */
    protected static function normalizeMediaSplitBlock(array $block): array
    {
        $imagePosition = (string) ($block['image_position'] ?? 'left');
        $imagePosition = array_key_exists($imagePosition, self::IMAGE_POSITION_OPTIONS)
            ? $imagePosition
            : 'left';

        $imageShape = (string) ($block['image_shape'] ?? 'acorn');
        $imageShape = array_key_exists($imageShape, self::IMAGE_SHAPE_OPTIONS)
            ? $imageShape
            : 'acorn';

        return [
            'type' => self::TYPE_MEDIA_SPLIT,
            'image_position' => $imagePosition,
            'image_shape' => $imageShape,
            'image' => MediaUrl::normalizeStoredPath($block['image'] ?? ''),
            'tagline' => trim((string) ($block['tagline'] ?? '')),
            'title' => trim((string) ($block['title'] ?? '')),
            'content' => (string) ($block['content'] ?? ''),
            'buttons' => self::normalizeButtons($block['buttons'] ?? []),
        ];
    }
}
