<?php

namespace App\Support\CategoryListTemplate;

class EventsCpdTemplate
{
    public const EVENT_NAME_KEY = 'event_name';

    /** @var array<int, array{slug: string, keywords: array<int, string>}> */
    private const REGISTRATION_RULES = [
        ['slug' => 'open-course', 'keywords' => ['公开课', 'open-course']],
        ['slug' => 'china-online', 'keywords' => ['中文直播', 'china-online']],
        ['slug' => 'china-offline', 'keywords' => ['中文线下', 'china-offline']],
        ['slug' => 'english-online', 'keywords' => ['英文线上', 'english-online']],
    ];

    /**
     * @param  array<string, mixed>|null  $extraFields
     */
    public static function registrationUrl(?array $extraFields): ?string
    {
        $eventName = is_array($extraFields)
            ? trim((string) ($extraFields[self::EVENT_NAME_KEY] ?? ''))
            : '';

        if ($eventName === '') {
            return null;
        }

        foreach (self::REGISTRATION_RULES as $rule) {
            if ($eventName === $rule['slug']) {
                return route('category.show', $rule['slug']);
            }
        }

        foreach (self::REGISTRATION_RULES as $rule) {
            foreach ($rule['keywords'] as $keyword) {
                if ($eventName === $keyword || str_contains($eventName, $keyword)) {
                    return route('category.show', $rule['slug']);
                }
            }
        }

        return null;
    }
}
