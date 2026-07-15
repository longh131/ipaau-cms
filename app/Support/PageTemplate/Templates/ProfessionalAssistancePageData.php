<?php

namespace App\Support\PageTemplate\Templates;

use App\Models\Page;
use App\Support\PageTemplate\PageBodyBlocks;
use App\Support\PageTemplate\ProfessionalAssistanceSections;
use App\Support\RichContent;

class ProfessionalAssistancePageData
{
    /**
     * @return array{sections: array<int, array<string, mixed>>}
     */
    public static function emptyStorage(): array
    {
        return [
            'sections' => [],
        ];
    }

    /**
     * @param  array<string, mixed>|null  $data
     * @return array{sections: array<int, array<string, mixed>>}
     */
    public static function forForm(?array $data): array
    {
        $data = is_array($data) ? $data : [];

        return [
            'sections' => ProfessionalAssistanceSections::forForm($data['sections'] ?? []),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array{sections: array<int, array<string, mixed>>}
     */
    public static function forStorage(array $data): array
    {
        return [
            'sections' => ProfessionalAssistanceSections::forStorage(
                static::forForm($data)['sections'],
            ),
        ];
    }

    /**
     * @param  array<string, mixed>|null  $data
     * @return array{
     *     sections: array<int, array<string, mixed>>,
     *     has_content: bool,
     *     needs_about_scripts: bool
     * }
     */
    public static function forFrontend(?array $data, Page $page): array
    {
        $form = static::forForm($data);
        $sections = ProfessionalAssistanceSections::forFrontend($form['sections']);

        return [
            'sections' => $sections,
            'has_content' => $sections !== [],
            'needs_about_scripts' => self::needsAboutScripts($form['sections']),
        ];
    }

    /**
     * @param  array<int, array<string, mixed>>  $sections
     */
    protected static function needsAboutScripts(array $sections): bool
    {
        if (PageBodyBlocks::needsAboutPageScripts($sections)) {
            return true;
        }

        foreach ($sections as $section) {
            $type = (string) ($section['type'] ?? '');

            if (in_array($type, [
                ProfessionalAssistanceSections::TYPE_CAROUSEL,
                ProfessionalAssistanceSections::TYPE_NEWS_LIST_A,
            ], true)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param  array<string, mixed>|null  $data
     */
    public static function hasContent(?array $data): bool
    {
        return ProfessionalAssistanceSections::hasContent(
            static::forForm($data)['sections'],
        );
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public static function contentSnapshot(array $data): string
    {
        $form = static::forForm($data);
        $parts = [];

        foreach ($form['sections'] as $section) {
            $type = (string) ($section['type'] ?? '');

            if ($type === ProfessionalAssistanceSections::TYPE_HTML_BODY) {
                if (filled($section['tagline'] ?? null)) {
                    $parts[] = '<p>'.e($section['tagline']).'</p>';
                }

                if (filled($section['title'] ?? null)) {
                    $parts[] = '<h2>'.e($section['title']).'</h2>';
                }

                if (filled(strip_tags((string) ($section['body'] ?? '')))) {
                    $parts[] = (string) $section['body'];
                }
            }

            if ($type === ProfessionalAssistanceSections::TYPE_RICH_TEXT) {
                if (filled($section['title'] ?? null)) {
                    $parts[] = '<h2>'.e($section['title']).'</h2>';
                }

                $html = RichContent::toHtml($section['html'] ?? '');

                if (filled(strip_tags($html))) {
                    $parts[] = $html;
                }
            }

            if ($type === ProfessionalAssistanceSections::TYPE_NEWS_LIST_A) {
                if (filled($section['section_title'] ?? null)) {
                    $parts[] = '<h2>'.e($section['section_title']).'</h2>';
                }

                foreach ($section['items'] ?? [] as $item) {
                    if (filled($item['tagline'] ?? null)) {
                        $parts[] = '<p>'.e($item['tagline']).'</p>';
                    }

                    if (filled($item['title'] ?? null)) {
                        $parts[] = '<h3>'.e($item['title']).'</h3>';
                    }

                    if (filled($item['summary'] ?? null)) {
                        $parts[] = '<p>'.e($item['summary']).'</p>';
                    }
                }
            }

            if ($type === ProfessionalAssistanceSections::TYPE_MEDIA_SPLIT) {
                if (filled($section['title'] ?? null)) {
                    $parts[] = '<h2>'.e($section['title']).'</h2>';
                }

                $html = RichContent::toHtml($section['content'] ?? '');

                if (RichContent::hasVisibleHtml($html)) {
                    $parts[] = $html;
                }
            }

            if ($type === ProfessionalAssistanceSections::TYPE_CAROUSEL) {
                if (filled($section['heading'] ?? null)) {
                    $parts[] = '<h2>'.e($section['heading']).'</h2>';
                }

                foreach ($section['slides'] ?? [] as $slide) {
                    if (filled($slide['quote'] ?? null)) {
                        $parts[] = '<p>'.e($slide['quote']).'</p>';
                    }
                }
            }
        }

        return implode("\n", $parts);
    }
}
