<?php

namespace App\Support\PageTemplate;

use App\Support\HomeSection\FaqSectionData;
use App\Support\HomeSection\NewsletterSectionData;
use App\Support\HomeSection\StatsSectionData;
use App\Support\HomeSection\TestimonialsSectionData;
use App\Support\MediaUrl;
use App\Support\PageTemplate\Templates\BasicContentPageData;
use App\Support\RichContent;

class GeneralSecondarySections
{
    public const TYPE_CONTENT_BLOCK = 'content_block';

    public const TYPE_FAQ = 'faq';

    public const TYPE_NEWS_LIST = 'news_list';

    public const TYPE_NEWS_LIST_A = 'news_list_a';

    public const TYPE_STATS = 'stats';

    public const TYPE_TESTIMONIALS = 'testimonials';

    public const TYPE_NEWSLETTER = 'newsletter';

    public const TYPE_HTML_BODY = 'html_body';

    public const TYPE_LEFT_RIGHT_LAYOUT = 'left_right_layout';

    public const TYPE_TABBED_CONTENT = 'tabbed_content';

    public const TYPE_MEDIA_SPLIT = 'media_split';

    public const NEWS_LIST_INITIAL_VISIBLE = 3;

    public const GENERAL_SECONDARY_NEWS_LIST_INITIAL_VISIBLE = 4;

    public const NEWS_LIST_BG_GRAY = 'gray';

    public const NEWS_LIST_BG_TRANSPARENT = 'transparent';

    /** @var array<string, string> */
    public const NEWS_LIST_BACKGROUND_OPTIONS = [
        self::NEWS_LIST_BG_GRAY => '灰色背景',
        self::NEWS_LIST_BG_TRANSPARENT => '透明背景',
    ];

    public const NEWS_LIST_BG_GRAY_COLOR = '#CFD5E2';

    /** @var array<string, string> */
    public const TYPE_OPTIONS = [
        self::TYPE_CONTENT_BLOCK => '富文本模块',
        self::TYPE_FAQ => '手风琴 FAQ',
        self::TYPE_NEWS_LIST_A => '新闻列表A',
        self::TYPE_NEWS_LIST => '新闻列表B',
        self::TYPE_STATS => '数据统计',
        self::TYPE_TESTIMONIALS => '会员推荐',
        self::TYPE_NEWSLETTER => '邮件订阅',
        self::TYPE_HTML_BODY => '正文（HTML 源码）',
        self::TYPE_LEFT_RIGHT_LAYOUT => '左右结构',
        self::TYPE_TABBED_CONTENT => '选项卡内容',
        self::TYPE_MEDIA_SPLIT => '图文分栏',
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
                self::TYPE_NEWS_LIST_A => self::normalizeNewsListAForForm($section),
                self::TYPE_NEWS_LIST => self::normalizeNewsListForForm($section),
                self::TYPE_STATS => self::normalizeStatsForForm($section),
                self::TYPE_TESTIMONIALS => self::normalizeTestimonialsForForm($section),
                self::TYPE_NEWSLETTER => self::normalizeNewsletterForForm($section),
                self::TYPE_HTML_BODY => self::normalizeHtmlBodyForForm($section),
                self::TYPE_LEFT_RIGHT_LAYOUT => self::normalizeLeftRightLayoutForForm($section),
                self::TYPE_TABBED_CONTENT => self::normalizeTabbedContentForForm($section),
                self::TYPE_MEDIA_SPLIT => PageBodyBlocks::mediaSplitSectionForForm($section),
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

            if ($section['type'] === self::TYPE_NEWS_LIST_A) {
                $section['items'] = self::normalizeDefaultNewsListItems($section['items'] ?? []);
            }

            if ($section['type'] === self::TYPE_NEWS_LIST) {
                $section['items'] = self::normalizeNewsListItems($section['items'] ?? []);
            }

            if ($section['type'] === self::TYPE_STATS) {
                $section['items'] = StatsSectionData::forStorage(['items' => $section['items'] ?? []])['items'];
            }

            if ($section['type'] === self::TYPE_TESTIMONIALS) {
                $section['items'] = TestimonialsSectionData::forStorage(['items' => $section['items'] ?? []])['items'];
            }

            if ($section['type'] === self::TYPE_NEWSLETTER) {
                $newsletter = NewsletterSectionData::forStorage([
                    'title' => $section['title'] ?? '',
                    'content' => $section['content'] ?? '',
                    'button_text' => $section['button_text'] ?? '',
                ]);
                $section['title'] = $newsletter['title'];
                $section['content'] = $newsletter['content'];
                $section['button_text'] = $newsletter['button_text'];
            }

            if ($section['type'] === self::TYPE_HTML_BODY) {
                $section['body'] = trim((string) ($section['body'] ?? ''));
            }

            if ($section['type'] === self::TYPE_LEFT_RIGHT_LAYOUT) {
                $section['buttons'] = self::normalizeButtons($section['buttons'] ?? []);
                $section['title_gradient'] = PageBodyBlocks::normalizeGradient((string) ($section['title_gradient'] ?? ''));
            }

            if ($section['type'] === self::TYPE_TABBED_CONTENT) {
                $section['tabs'] = GeneralSecondaryTabbedContentSectionData::forStorage([
                    'tabs' => $section['tabs'] ?? [],
                ])['tabs'];
            }

            if ($section['type'] === self::TYPE_MEDIA_SPLIT) {
                $section = PageBodyBlocks::mediaSplitSectionForStorage($section);
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
                    'tagline' => $section['tagline'],
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
                self::TYPE_NEWS_LIST_A => [
                    'type' => self::TYPE_NEWS_LIST_A,
                    'section_title' => $section['section_title'],
                    'summary_html' => RichContent::toHtml($section['summary'] ?? ''),
                    'view_more_label' => $section['view_more_label'],
                    'section_background' => self::normalizeNewsListBackground((string) ($section['section_background'] ?? '')),
                    'initial_visible' => self::NEWS_LIST_INITIAL_VISIBLE,
                    'items' => self::normalizeDefaultNewsListItemsForFrontend($section['items'] ?? []),
                ],
                self::TYPE_NEWS_LIST => [
                    'type' => self::TYPE_NEWS_LIST,
                    'section_title' => $section['section_title'],
                    'summary_html' => RichContent::toHtml($section['summary'] ?? ''),
                    'view_more_label' => $section['view_more_label'],
                    'section_background' => self::normalizeNewsListBackground((string) ($section['section_background'] ?? '')),
                    'initial_visible' => self::GENERAL_SECONDARY_NEWS_LIST_INITIAL_VISIBLE,
                    'items' => self::normalizeNewsListItemsForFrontend($section['items'] ?? []),
                ],
                self::TYPE_STATS => [
                    'type' => self::TYPE_STATS,
                    'items' => StatsSectionData::forFrontend(['items' => $section['items']])['items'],
                ],
                self::TYPE_TESTIMONIALS => [
                    'type' => self::TYPE_TESTIMONIALS,
                    'section_title' => trim((string) ($section['section_title'] ?? '')),
                    'items' => TestimonialsSectionData::forFrontend(['items' => $section['items']])['items'],
                ],
                self::TYPE_NEWSLETTER => [
                    'type' => self::TYPE_NEWSLETTER,
                    ...NewsletterSectionData::forFrontend([
                        'title' => $section['title'] ?? '',
                        'content' => $section['content'] ?? '',
                        'button_text' => $section['button_text'] ?? '',
                    ]),
                ],
                self::TYPE_HTML_BODY => [
                    'type' => self::TYPE_HTML_BODY,
                    'body_html' => trim((string) ($section['body'] ?? '')),
                ],
                self::TYPE_LEFT_RIGHT_LAYOUT => [
                    'type' => self::TYPE_LEFT_RIGHT_LAYOUT,
                    'tagline' => $section['tagline'],
                    'title' => $section['title'],
                    'title_gradient_class' => PageBodyBlocks::gradientClass((string) ($section['title_gradient'] ?? '')),
                    'content_html' => RichContent::toHtml($section['content']),
                    'buttons' => $section['buttons'],
                ],
                self::TYPE_TABBED_CONTENT => [
                    'type' => self::TYPE_TABBED_CONTENT,
                    ...GeneralSecondaryTabbedContentSectionData::forFrontend([
                        'tabs' => $section['tabs'] ?? [],
                    ]),
                ],
                self::TYPE_MEDIA_SPLIT => PageBodyBlocks::mediaSplitSectionForFrontend($section),
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
            'tagline' => trim((string) ($section['tagline'] ?? '')),
            'title' => trim((string) ($section['title'] ?? '')),
            'title_align' => self::normalizeTitleAlign((string) ($section['title_align'] ?? 'left')),
            'content' => RichContent::encodeDocumentForForm($section['content'] ?? ''),
            'buttons' => self::normalizeButtonsForForm($section['buttons'] ?? []),
        ];
    }

    /**
     * @param  array<string, mixed>  $section
     * @return array<string, mixed>
     */
    protected static function normalizeFaqForForm(array $section): array
    {
        $items = FaqSectionData::forForm(['items' => $section['items'] ?? []])['items'];

        return [
            'type' => self::TYPE_FAQ,
            'tagline' => trim((string) ($section['tagline'] ?? '')),
            'title' => trim((string) ($section['title'] ?? '')),
            'intro' => trim((string) ($section['intro'] ?? '')),
            'items' => collect($items)
                ->map(function (array $item): array {
                    $item['answer'] = RichContent::encodeDocumentForForm($item['answer'] ?? '');

                    return $item;
                })
                ->all(),
        ];
    }

    /**
     * @param  array<string, mixed>  $section
     * @return array<string, mixed>
     */
    protected static function normalizeStatsForForm(array $section): array
    {
        return [
            'type' => self::TYPE_STATS,
            'items' => StatsSectionData::forForm(['items' => $section['items'] ?? []])['items'],
        ];
    }

    /**
     * @param  array<string, mixed>  $section
     * @return array<string, mixed>
     */
    protected static function normalizeTestimonialsForForm(array $section): array
    {
        return [
            'type' => self::TYPE_TESTIMONIALS,
            'section_title' => trim((string) ($section['section_title'] ?? '')),
            'items' => TestimonialsSectionData::forForm(['items' => $section['items'] ?? []])['items'],
        ];
    }

    /**
     * @param  array<string, mixed>  $section
     * @return array<string, mixed>
     */
    protected static function normalizeNewsletterForForm(array $section): array
    {
        $newsletter = NewsletterSectionData::forForm([
            'title' => $section['title'] ?? '',
            'content' => $section['content'] ?? '',
            'button_text' => $section['button_text'] ?? '',
        ], nestedEditor: true);

        return [
            'type' => self::TYPE_NEWSLETTER,
            'title' => $newsletter['title'],
            'content' => $newsletter['content'],
            'button_text' => $newsletter['button_text'],
        ];
    }

    /**
     * @param  array<string, mixed>  $section
     * @return array<string, mixed>
     */
    protected static function normalizeHtmlBodyForForm(array $section): array
    {
        return [
            'type' => self::TYPE_HTML_BODY,
            'body' => BasicContentPageData::normalizeBodyForForm($section['body'] ?? ''),
        ];
    }

    /**
     * @param  array<string, mixed>  $section
     * @return array<string, mixed>
     */
    protected static function normalizeLeftRightLayoutForForm(array $section): array
    {
        return [
            'type' => self::TYPE_LEFT_RIGHT_LAYOUT,
            'tagline' => trim((string) ($section['tagline'] ?? '')),
            'title' => trim((string) ($section['title'] ?? '')),
            'title_gradient' => PageBodyBlocks::normalizeGradient((string) ($section['title_gradient'] ?? '')),
            'content' => RichContent::encodeDocumentForForm($section['content'] ?? ''),
            'buttons' => self::normalizeButtonsForForm($section['buttons'] ?? []),
        ];
    }

    /**
     * @param  array<string, mixed>  $section
     * @return array<string, mixed>
     */
    protected static function normalizeTabbedContentForForm(array $section): array
    {
        return [
            'type' => self::TYPE_TABBED_CONTENT,
            ...GeneralSecondaryTabbedContentSectionData::forForm([
                'tabs' => $section['tabs'] ?? [],
            ]),
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
            'section_background' => self::normalizeNewsListBackground((string) ($section['section_background'] ?? '')),
            'items' => self::normalizeDefaultNewsListItemsForForm($section['items'] ?? []),
        ];
    }

    protected static function normalizeNewsListForForm(array $section): array
    {
        $viewMoreLabel = trim((string) ($section['view_more_label'] ?? ''));

        return [
            'type' => self::TYPE_NEWS_LIST,
            'section_title' => trim((string) ($section['section_title'] ?? '')),
            'summary' => RichContent::encodeDocumentForForm($section['summary'] ?? ''),
            'view_more_label' => filled($viewMoreLabel) ? $viewMoreLabel : '查看更多',
            'section_background' => self::normalizeNewsListBackground((string) ($section['section_background'] ?? '')),
            'items' => self::normalizeNewsListItemsForForm($section['items'] ?? []),
        ];
    }

    public static function normalizeNewsListBackground(string $background): string
    {
        return array_key_exists($background, self::NEWS_LIST_BACKGROUND_OPTIONS)
            ? $background
            : self::NEWS_LIST_BG_GRAY;
    }

    /**
     * @return array<int, array{
     *     title: string,
     *     summary: string,
     *     icon: string,
     *     link_title: string,
     *     url: string,
     *     target: string
     * }>
     */
    /**
     * @return array<int, array{
     *     title: string,
     *     summary: string,
     *     url: string,
     *     target: string
     * }>
     */
    protected static function normalizeDefaultNewsListItems(mixed $items): array
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
     * @return array<int, array{
     *     title: string,
     *     summary: string,
     *     url: string,
     *     target: string
     * }>
     */
    protected static function normalizeDefaultNewsListItemsForFrontend(mixed $items): array
    {
        return self::normalizeDefaultNewsListItems($items);
    }

    /**
     * @return array<int, array{
     *     title: string,
     *     summary: string,
     *     url: string,
     *     target: string
     * }>
     */
    protected static function normalizeDefaultNewsListItemsForForm(mixed $items): array
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
            $icon = MediaUrl::normalizeStoredPath($item['icon'] ?? '');
            $linkTitle = trim((string) ($item['link_title'] ?? ''));
            $url = trim((string) ($item['url'] ?? ''));
            $target = (string) ($item['target'] ?? '');
            $target = $target === '_blank' ? '_blank' : '';

            $normalized[] = [
                'title' => $title,
                'summary' => $summary,
                'icon' => $icon,
                'link_title' => $linkTitle,
                'url' => $url,
                'target' => $target,
            ];
        }

        return $normalized;
    }

    /**
     * @return array<int, array{
     *     title: string,
     *     summary: string,
     *     icon: ?string,
     *     link_title: string,
     *     url: string,
     *     target: string
     * }>
     */
    protected static function normalizeNewsListItemsForFrontend(mixed $items): array
    {
        return collect(self::normalizeNewsListItems($items))
            ->map(fn (array $item): array => [
                'title' => $item['title'],
                'summary' => $item['summary'],
                'icon' => filled($item['icon']) ? MediaUrl::resolve($item['icon']) : null,
                'link_title' => $item['link_title'],
                'url' => $item['url'],
                'target' => $item['target'],
            ])
            ->all();
    }

    /**
     * @return array<int, array{
     *     title: string,
     *     summary: string,
     *     icon: string,
     *     link_title: string,
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
            $summary = trim((string) ($item['summary'] ?? ''));
            $icon = MediaUrl::normalizeStoredPath($item['icon'] ?? '');
            $linkTitle = trim((string) ($item['link_title'] ?? ''));
            $url = trim((string) ($item['url'] ?? ''));

            if ($title === '' && $summary === '' && $url === '' && $icon === '') {
                continue;
            }

            $target = (string) ($item['target'] ?? '');
            $target = $target === '_blank' ? '_blank' : '';

            $normalized[] = [
                'title' => $title,
                'summary' => $summary,
                'icon' => $icon,
                'link_title' => $linkTitle,
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
            self::TYPE_CONTENT_BLOCK => filled($section['tagline'] ?? null)
                || filled($section['title'] ?? null)
                || filled(strip_tags(RichContent::toHtml($section['content'] ?? '')))
                || ($section['buttons'] ?? []) !== [],
            self::TYPE_FAQ => ($section['items'] ?? []) !== [],
            self::TYPE_NEWS_LIST_A, self::TYPE_NEWS_LIST => filled($section['section_title'] ?? null)
                || filled(strip_tags(RichContent::toHtml($section['summary'] ?? '')))
                || ($section['items'] ?? []) !== [],
            self::TYPE_STATS => ($section['items'] ?? []) !== [],
            self::TYPE_TESTIMONIALS => filled($section['section_title'] ?? null)
                || ($section['items'] ?? []) !== [],
            self::TYPE_NEWSLETTER => filled($section['title'] ?? null)
                || filled(strip_tags(RichContent::toHtml($section['content'] ?? ''))),
            self::TYPE_HTML_BODY => RichContent::hasVisibleHtml((string) ($section['body'] ?? '')),
            self::TYPE_LEFT_RIGHT_LAYOUT => filled($section['tagline'] ?? null)
                || filled($section['title'] ?? null)
                || filled(strip_tags(RichContent::toHtml($section['content'] ?? '')))
                || ($section['buttons'] ?? []) !== [],
            self::TYPE_TABBED_CONTENT => (GeneralSecondaryTabbedContentSectionData::forStorage([
                'tabs' => $section['tabs'] ?? [],
            ])['tabs']) !== [],
            self::TYPE_MEDIA_SPLIT => PageBodyBlocks::mediaSplitSectionHasContent($section),
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

    /**
     * @return array<int, array{label: string, url: string, style: string, target: string}>
     */
    public static function buttonsForStorage(mixed $buttons): array
    {
        return self::normalizeButtons($buttons);
    }

    /**
     * @return array<int, array{label: string, url: string, style: string, target: string}>
     */
    public static function buttonsForForm(mixed $buttons): array
    {
        return self::normalizeButtonsForForm($buttons);
    }
}
