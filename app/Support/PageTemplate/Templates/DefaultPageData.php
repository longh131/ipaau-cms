<?php

namespace App\Support\PageTemplate\Templates;

use App\Models\Page;
use App\Support\PageTemplate\PageBodyBlocks;

class DefaultPageData
{
    /**
     * @return array{body_blocks: array<int, array<string, mixed>>}
     */
    public static function emptyStorage(): array
    {
        return [
            'body_blocks' => [],
        ];
    }

    /**
     * @param  array<string, mixed>|null  $data
     * @return array{body_blocks: array<int, array<string, mixed>>}
     */
    public static function forForm(?array $data): array
    {
        $data = is_array($data) ? $data : [];

        return [
            'body_blocks' => PageBodyBlocks::forForm($data['body_blocks'] ?? null),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array{body_blocks: array<int, array<string, mixed>>}
     */
    public static function forStorage(array $data): array
    {
        return [
            'body_blocks' => PageBodyBlocks::forStorage(
                static::forForm($data)['body_blocks'],
            ),
        ];
    }

    /**
     * @param  array<string, mixed>|null  $data
     * @return array{
     *     body_blocks: array<int, array<string, mixed>>,
     *     has_body: bool,
     *     needs_about_scripts: bool
     * }
     */
    public static function forFrontend(?array $data, Page $page): array
    {
        $form = static::forForm($data);
        $bodyBlocks = PageBodyBlocks::forFrontend($form['body_blocks'], $page->content);

        return [
            'body_blocks' => $bodyBlocks,
            'has_body' => $bodyBlocks !== [],
            'needs_about_scripts' => PageBodyBlocks::needsAboutPageScripts($form['body_blocks']),
        ];
    }
}
