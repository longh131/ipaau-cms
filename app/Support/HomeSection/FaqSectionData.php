<?php

namespace App\Support\HomeSection;

class FaqSectionData
{
    /**
     * @return array{items: array<int, array<string, mixed>>}
     */
    public static function emptyStorage(): array
    {
        return [
            'items' => [],
        ];
    }

    /**
     * @return array{items: array<int, array{question: string, answer: string}>}
     */
    public static function defaultStorage(): array
    {
        return [
            'items' => [
                [
                    'question' => 'Who can join the IPA?',
                    'answer' => 'Anyone with a passion for accounting and business can become a member — from students and emerging professionals to experienced practitioners. We also welcome affiliates and business advisers who want to be part of a trusted professional community.',
                ],
                [
                    'question' => 'How do I join the IPA?',
                    'answer' => 'Becoming a member is simple. Click "Become a Member," choose your membership type, and complete your application online. Our team is here to guide you every step of the way.',
                ],
                [
                    'question' => 'How do I book an event/webinar/course?',
                    'answer' => "Simple! If you're an IPA member login to your account and add the event to your cart and hit purchase. If you are not a member of the IPA you simply need to create an account log in. Once activated, log in, add your desired event to cart and purchase!",
                ],
                [
                    'question' => 'What are the benefits of membership?',
                    'answer' => 'IPA members gain access to professional recognition, practical resources, career support, free and discounted CPD opportunities, networking events, and advocacy that ensures your voice is heard in shaping the future of the profession.',
                ],
            ],
        ];
    }

    /**
     * @param  array<string, mixed>|null  $data
     * @return array{items: array<int, array{question: string, answer: string}>}
     */
    public static function forForm(?array $data): array
    {
        $data = is_array($data) ? $data : [];
        $items = [];

        foreach ($data['items'] ?? [] as $item) {
            if (! is_array($item)) {
                continue;
            }

            $normalized = static::normalizeItem($item);

            if ($normalized['question'] === '' && $normalized['answer'] === '') {
                continue;
            }

            $items[] = $normalized;
        }

        return ['items' => $items];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array{items: array<int, array{question: string, answer: string}>}
     */
    public static function forStorage(array $data): array
    {
        $items = [];

        foreach ($data['items'] ?? [] as $item) {
            if (! is_array($item)) {
                continue;
            }

            $normalized = static::normalizeItem($item);

            if ($normalized['question'] === '') {
                continue;
            }

            $items[] = $normalized;
        }

        return ['items' => $items];
    }

    /**
     * @param  array<string, mixed>|null  $data
     * @return array{items: array<int, array{question: string, answer: string}>}
     */
    public static function forFrontend(?array $data): array
    {
        $form = static::forForm($data);

        return [
            'items' => collect($form['items'])
                ->filter(fn (array $item) => filled($item['question']))
                ->values()
                ->all(),
        ];
    }

    /**
     * @param  array<string, mixed>  $item
     * @return array{question: string, answer: string}
     */
    private static function normalizeItem(array $item): array
    {
        return [
            'question' => trim((string) ($item['question'] ?? $item['title'] ?? '')),
            'answer' => trim((string) ($item['answer'] ?? $item['content'] ?? $item['body'] ?? '')),
        ];
    }
}
