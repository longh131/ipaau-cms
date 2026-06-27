<?php

namespace App\Support\HomeSection;

use App\Support\MediaUrl;

class CtaSectionData extends BasicContentSectionData
{
    /**
     * @return array{
     *     tagline: string,
     *     title_lines: array<int, array{text: string}>,
     *     description: string,
     *     buttons: array<int, array{label: string, url: string, style: string, target: ?string}>,
     *     image: string
     * }
     */
    public static function defaultStorage(): array
    {
        return [
            'tagline' => 'shaping the future',
            'title_lines' => [
                ['text' => 'Driving industry change, inclusion and diversity.'],
            ],
            'description' => "We're not just responding to change—we're driving it.\nThrough innovation, advocacy, and education, we're equipping our members with the skills and insight to thrive in an evolving world of business.\n\nWe're championing digital transformation, supporting sustainable practices, and investing in future-focused learning that ensures our members remain at the forefront of the profession.\n\nFrom empowering small business advisers to embracing emerging technologies, we're shaping a future where accountants continue to play a vital role—as trusted partners, ethical leaders, and agents of positive change.",
            'buttons' => [
                [
                    'label' => 'HAVE YOUR SAY',
                    'url' => '/',
                    'style' => 'secondary',
                    'target' => null,
                ],
            ],
            'image' => '',
        ];
    }

    /**
     * @param  array<string, mixed>|null  $data
     * @return array{
     *     tagline: string,
     *     title_lines: array<int, array{text: string}>,
     *     description: string,
     *     buttons: array<int, array{label: string, url: string, style: string, target: string}>,
     *     image: string
     * }
     */
    public static function forForm(?array $data): array
    {
        $data = is_array($data) ? $data : [];

        $basic = parent::forForm($data);

        return [
            ...$basic,
            'image' => MediaUrl::normalizeStoredPath($data['image'] ?? ''),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array{
     *     tagline: string,
     *     title_lines: array<int, array{text: string}>,
     *     description: string,
     *     buttons: array<int, array{label: string, url: string, style: string, target: ?string}>,
     *     image: string
     * }
     */
    public static function forStorage(array $data): array
    {
        $form = static::forForm($data);
        $stored = parent::forStorage($form);

        return [
            ...$stored,
            'image' => MediaUrl::normalizeStoredPath($form['image'] ?? ''),
        ];
    }

    /**
     * @param  array<string, mixed>|null  $data
     * @return array{
     *     tagline: string,
     *     title_lines: array<int, string>,
     *     description: string,
     *     buttons: array<int, array{label: string, url: string, target: ?string, style: string}>,
     *     image: ?string
     * }
     */
    public static function forFrontend(?array $data): array
    {
        $form = static::forForm($data);
        $basic = parent::forFrontend($data);

        return [
            ...$basic,
            'image' => MediaUrl::resolve($form['image']),
        ];
    }
}
