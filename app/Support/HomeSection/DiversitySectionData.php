<?php

namespace App\Support\HomeSection;

use App\Support\MediaUrl;

class DiversitySectionData
{
    /**
     * @return array{title: string, image: string}
     */
    public static function emptyStorage(): array
    {
        return [
            'title' => '',
            'image' => '',
        ];
    }

    /**
     * @return array{title: string, image: string}
     */
    public static function defaultStorage(): array
    {
        return [
            'title' => <<<'HTML'
<h2 class="diversity-section__heading">
Independent minds shaping a
<span class="text-gradient-pink-reverse">diverse</span>
and
<span class="text-gradient-pink">innovative</span> future
for accounting.
</h2>
HTML,
            'image' => '',
        ];
    }

    /**
     * @param  array<string, mixed>|null  $data
     * @return array{title: string, image: string}
     */
    public static function forForm(?array $data): array
    {
        $data = is_array($data) ? $data : [];

        if (static::isLegacyFormat($data)) {
            $data = static::migrateLegacy($data);
        }

        return [
            'title' => trim((string) ($data['title'] ?? '')),
            'image' => MediaUrl::normalizeStoredPath($data['image'] ?? ''),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array{title: string, image: string}
     */
    public static function forStorage(array $data): array
    {
        $form = static::forForm($data);
        $title = trim($form['title']);
        $image = $form['image'];

        if ($title === '' && $image === '') {
            return static::emptyStorage();
        }

        return [
            'title' => $title,
            'image' => $image,
        ];
    }

    /**
     * @param  array<string, mixed>|null  $data
     * @return array{title_html: string, image: ?string}
     */
    public static function forFrontend(?array $data): array
    {
        $form = static::forForm($data);
        $title = trim($form['title']);

        return [
            'title_html' => static::normalizeTitleHtml($title),
            'image' => MediaUrl::resolve($form['image']),
        ];
    }

    private static function normalizeTitleHtml(string $title): string
    {
        $title = trim($title);

        if ($title === '' || blank(trim(strip_tags($title)))) {
            return '';
        }

        if (! preg_match('/<h[1-3]\b/i', $title)) {
            $title = '<h2 class="diversity-section__heading">'.$title.'</h2>';
        }

        return $title;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private static function isLegacyFormat(array $data): bool
    {
        return array_key_exists('body', $data)
            && ! array_key_exists('image', $data);
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array{title: string, image: string}
     */
    private static function migrateLegacy(array $data): array
    {
        $title = trim((string) ($data['title'] ?? ''));
        $body = trim((string) ($data['body'] ?? ''));

        if ($title !== '' && ! str_contains($title, '<')) {
            $title = sprintf(
                '<h2 class="diversity-section__heading">%s</h2>',
                e($title),
            );
        } elseif ($title === '' && $body !== '') {
            $title = sprintf(
                '<h2 class="diversity-section__heading">%s</h2>',
                e($body),
            );
        } elseif ($title !== '' && ! preg_match('/<h[1-3]\b/i', $title)) {
            $title = '<h2 class="diversity-section__heading">'.$title.'</h2>';
        }

        return [
            'title' => $title,
            'image' => '',
        ];
    }
}
