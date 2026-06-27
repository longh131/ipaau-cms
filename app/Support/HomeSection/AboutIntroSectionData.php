<?php

namespace App\Support\HomeSection;

class AboutIntroSectionData extends BasicContentSectionData
{
    /**
     * @return array{
     *     tagline: string,
     *     title_lines: array<int, array{text: string}>,
     *     description: string,
     *     buttons: array<int, array{label: string, url: string, style: string, target: ?string}>
     * }
     */
    public static function defaultStorage(): array
    {
        return [
            'tagline' => 'ABOUT THE IPA',
            'title_lines' => [
                ['text' => "We believe that good support goes beyond numbers — it's about people, purpose, and positive impact"],
            ],
            'description' => "For over a century, we've supported accountants and business professionals who play a vital role in helping small business and communities thrive. Our members are trusted advisers, innovators and leaders who share a common goal: to make a meaningful difference.\n\nWe're proud to be one of Australia's three recognised professional accounting bodies, with a growing global footprint and a commitment to lifelong learning, ethical practice, and community connection.\n\nThrough advocacy, education and support, we're shaping a profession that's ready for the future — and grounded in the values that matter most.",
            'buttons' => [
                [
                    'label' => 'LEARN MORE',
                    'url' => '/',
                    'style' => 'primary',
                    'target' => null,
                ],
                [
                    'label' => 'OUR TEAM',
                    'url' => '/',
                    'style' => 'secondary',
                    'target' => null,
                ],
            ],
        ];
    }
}
