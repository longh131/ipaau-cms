<?php

namespace App\Support\HomeSection;

class NewsletterSectionData
{
    /**
     * @return array{title: string, content: string, button_text: string}
     */
    public static function emptyStorage(): array
    {
        return [
            'title' => '',
            'content' => '',
            'button_text' => '提交',
        ];
    }

    /**
     * @return array{title: string, content: string, button_text: string}
     */
    public static function defaultStorage(): array
    {
        return [
            'title' => 'Subscribe to our newsletter',
            'content' => <<<'HTML'
<h1>Join our 2,000 subscribers to get the latest updates delivered straight to your inbox—no spam, just the good stuff.</h1>
<ul>
<li>Stay informed with the latest news, trends, and tips</li>
<li>Quick reads designed to fit your busy schedule</li>
<li>No spam—ever, just the good stuff</li>
</ul>
HTML,
            'button_text' => '提交',
        ];
    }

    /**
     * @param  array<string, mixed>|null  $data
     * @return array{title: string, content: string, button_text: string}
     */
    public static function forForm(?array $data): array
    {
        $data = is_array($data) ? $data : [];

        if (static::isLegacyFormat($data)) {
            $data = static::migrateLegacy($data);
        }

        $buttonText = trim((string) ($data['button_text'] ?? ''));

        return [
            'title' => trim((string) ($data['title'] ?? '')),
            'content' => trim((string) ($data['content'] ?? '')),
            'button_text' => $buttonText !== '' ? $buttonText : '提交',
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array{title: string, content: string, button_text: string}
     */
    public static function forStorage(array $data): array
    {
        $form = static::forForm($data);

        if ($form['title'] === '' && blank(trim(strip_tags($form['content'])))) {
            return static::emptyStorage();
        }

        return $form;
    }

    /**
     * @param  array<string, mixed>|null  $data
     * @return array{title: string, content_html: string, button_text: string}
     */
    public static function forFrontend(?array $data): array
    {
        $form = static::forForm($data);

        return [
            'title' => $form['title'],
            'content_html' => blank(trim(strip_tags($form['content']))) ? '' : $form['content'],
            'button_text' => $form['button_text'],
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private static function isLegacyFormat(array $data): bool
    {
        return array_key_exists('subtitle', $data)
            && ! array_key_exists('content', $data);
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array{title: string, content: string, button_text: string}
     */
    private static function migrateLegacy(array $data): array
    {
        $title = trim((string) ($data['title'] ?? ''));
        $subtitle = trim((string) ($data['subtitle'] ?? ''));
        $content = $subtitle !== '' ? '<p>'.e($subtitle).'</p>' : '';

        $buttonText = trim((string) ($data['button_text'] ?? ''));

        return [
            'title' => $title,
            'content' => $content,
            'button_text' => $buttonText !== '' ? $buttonText : '提交',
        ];
    }
}
