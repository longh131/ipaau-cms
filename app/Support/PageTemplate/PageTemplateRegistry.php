<?php

namespace App\Support\PageTemplate;

use App\Models\Page;
use App\Support\PageTemplate\Templates\DefaultPageData;

class PageTemplateRegistry
{
    /** @var array<string, class-string> */
    private const DATA_CLASSES = [
        Page::TEMPLATE_DEFAULT => DefaultPageData::class,
    ];

    public static function dataClass(string $template): ?string
    {
        return self::DATA_CLASSES[$template] ?? null;
    }

    /**
     * @return array<string, mixed>
     */
    public static function emptyStorage(string $template): array
    {
        $class = self::dataClass($template);

        return $class ? $class::emptyStorage() : [];
    }

    /**
     * @param  array<string, mixed>|null  $data
     * @return array<string, mixed>
     */
    public static function forForm(?array $data, string $template): array
    {
        $class = self::dataClass($template);

        return $class ? $class::forForm($data) : [];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public static function forStorage(array $data, string $template): array
    {
        $class = self::dataClass($template);

        return $class ? $class::forStorage($data) : [];
    }

    /**
     * @param  array<string, mixed>|null  $data
     * @return array<string, mixed>
     */
    public static function forFrontend(?array $data, string $template, Page $page): array
    {
        $class = self::dataClass($template);

        return $class ? $class::forFrontend($data, $page) : [];
    }
}
