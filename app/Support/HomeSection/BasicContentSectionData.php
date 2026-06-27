<?php

namespace App\Support\HomeSection;

class BasicContentSectionData
{
    /**
     * @return array{
     *     tagline: string,
     *     title_lines: array<int, array{text: string}>,
     *     description: string,
     *     buttons: array<int, array{label: string, url: string, style: string, target: ?string}>
     * }
     */
    public static function emptyStorage(): array
    {
        return [
            'tagline' => '',
            'title_lines' => [],
            'description' => '',
            'buttons' => [],
        ];
    }

    /**
     * @param  array<string, mixed>|null  $data
     * @return array{
     *     tagline: string,
     *     title_lines: array<int, array{text: string}>,
     *     description: string,
     *     buttons: array<int, array{label: string, url: string, style: string, target: string}>
     * }
     */
    public static function forForm(?array $data): array
    {
        $data = is_array($data) ? $data : [];

        if (static::isLegacyFormat($data)) {
            $data = static::migrateLegacy($data);
        }

        $titleLines = [];

        foreach ($data['title_lines'] ?? [] as $line) {
            $text = is_array($line)
                ? trim((string) ($line['text'] ?? $line['line'] ?? ''))
                : trim((string) $line);

            if ($text !== '') {
                $titleLines[] = ['text' => $text];
            }
        }

        $buttons = [];

        foreach ($data['buttons'] ?? [] as $button) {
            if (! is_array($button)) {
                continue;
            }

            $label = trim((string) ($button['label'] ?? $button['name'] ?? ''));
            $url = trim((string) ($button['url'] ?? ''));

            if ($label === '' && $url === '') {
                continue;
            }

            $style = strtolower((string) ($button['style'] ?? 'secondary'));

            if (! in_array($style, ['primary', 'secondary'], true)) {
                $style = 'secondary';
            }

            $buttons[] = [
                'label' => $label,
                'url' => $url,
                'style' => $style,
                'target' => filled($button['target'] ?? null) ? (string) $button['target'] : '',
            ];
        }

        return [
            'tagline' => trim((string) ($data['tagline'] ?? '')),
            'title_lines' => $titleLines,
            'description' => trim((string) ($data['description'] ?? $data['body'] ?? '')),
            'buttons' => $buttons,
        ];
    }

    /**
     * @param  array<string, mixed>|null  $data
     * @return array{
     *     tagline: string,
     *     title_lines: array<int, string>,
     *     description: string,
     *     buttons: array<int, array{label: string, url: string, target: ?string, style: string}>
     * }
     */
    public static function forFrontend(?array $data): array
    {
        $form = static::forForm($data);

        return [
            'tagline' => $form['tagline'],
            'title_lines' => collect($form['title_lines'])
                ->pluck('text')
                ->values()
                ->all(),
            'description' => $form['description'],
            'buttons' => collect($form['buttons'])
                ->filter(fn (array $button) => filled($button['label']) && filled($button['url']))
                ->map(fn (array $button) => [
                    'label' => $button['label'],
                    'url' => $button['url'],
                    'style' => $button['style'],
                    'target' => filled($button['target']) ? $button['target'] : null,
                ])
                ->values()
                ->all(),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array{
     *     tagline: string,
     *     title_lines: array<int, array{text: string}>,
     *     description: string,
     *     buttons: array<int, array{label: string, url: string, style: string, target: ?string}>
     * }
     */
    public static function forStorage(array $data): array
    {
        $form = static::forForm($data);

        return [
            'tagline' => $form['tagline'],
            'title_lines' => $form['title_lines'],
            'description' => $form['description'],
            'buttons' => collect($form['buttons'])
                ->filter(fn (array $button) => filled($button['label']) && filled($button['url']))
                ->map(fn (array $button) => [
                    'label' => $button['label'],
                    'url' => $button['url'],
                    'style' => $button['style'],
                    'target' => filled($button['target']) ? $button['target'] : null,
                ])
                ->values()
                ->all(),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private static function isLegacyFormat(array $data): bool
    {
        return array_key_exists('eyebrow', $data)
            || array_key_exists('cta_text', $data)
            || array_key_exists('body', $data)
            || (array_key_exists('title', $data) && ! array_key_exists('title_lines', $data));
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private static function migrateLegacy(array $data): array
    {
        $titleLines = [];

        if (filled($data['title'] ?? null)) {
            $titleLines[] = ['text' => (string) $data['title']];
        }

        if (filled($data['subtitle'] ?? null)) {
            $titleLines[] = ['text' => (string) $data['subtitle']];
        }

        $buttons = [];

        if (filled($data['cta_text'] ?? null) && filled($data['cta_url'] ?? null)) {
            $buttons[] = [
                'label' => (string) $data['cta_text'],
                'url' => (string) $data['cta_url'],
                'target' => null,
                'style' => 'primary',
            ];
        }

        return [
            'tagline' => trim((string) ($data['eyebrow'] ?? $data['tagline'] ?? '')),
            'title_lines' => $titleLines,
            'description' => trim((string) ($data['description'] ?? $data['body'] ?? '')),
            'buttons' => $buttons,
        ];
    }
}
