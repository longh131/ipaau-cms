<?php

namespace App\Support\CategoryListTemplate;

class EventsCpdTemplate
{
    public const EVENT_NAME_KEY = 'event_name';

    public const START_DATE_KEY = 'course_start_date';

    /** @var array<int, string> */
    private const START_DATE_KEY_ALIASES = [
        'course_start_date',
        'start_date',
        'event_start_date',
        'class_start_date',
        'open_date',
    ];

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

    /**
     * @param  array<int, mixed>|null  $schema
     */
    public static function startDateKey(?array $schema): string
    {
        $normalized = \App\Support\ArticleExtraFields::normalizeSchema($schema);

        foreach ($normalized as $field) {
            if (($field['type'] ?? '') !== 'date') {
                continue;
            }

            $key = (string) ($field['key'] ?? '');
            $label = (string) ($field['label'] ?? '');

            if (in_array($key, self::START_DATE_KEY_ALIASES, true) || str_contains($label, '开课')) {
                return $key;
            }
        }

        return self::START_DATE_KEY;
    }

    /**
     * @param  array<string, mixed>|null  $extraFields
     * @param  array<int, mixed>|null  $schema
     */
    public static function startDate(?array $extraFields, ?array $schema = null): ?\Illuminate\Support\Carbon
    {
        $key = self::startDateKey($schema);
        $raw = is_array($extraFields) ? trim((string) ($extraFields[$key] ?? '')) : '';

        if ($raw === '') {
            return null;
        }

        try {
            return \Illuminate\Support\Carbon::parse($raw)->startOfDay();
        } catch (\Throwable) {
            return null;
        }
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Builder<\App\Models\Article>  $query
     * @return \Illuminate\Database\Eloquent\Builder<\App\Models\Article>
     */
    public static function applyStartDateFilter($query, string $key, ?string $from, ?string $to)
    {
        if (! preg_match('/^[a-z][a-z0-9_]*$/', $key)) {
            return $query;
        }

        $fromDate = self::parseFilterDate($from);
        $toDate = self::parseFilterDate($to);

        if ($fromDate && $toDate && $fromDate->gt($toDate)) {
            [$fromDate, $toDate] = [$toDate, $fromDate];
        }

        if (! $fromDate && ! $toDate) {
            return $query;
        }

        $path = '$.'.$key;

        $query->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(extra_fields, '{$path}')) IS NOT NULL")
            ->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(extra_fields, '{$path}')) <> ''");

        if ($fromDate) {
            $query->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(extra_fields, '{$path}')) >= ?", [$fromDate->format('Y-m-d')]);
        }

        if ($toDate) {
            $query->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(extra_fields, '{$path}')) <= ?", [$toDate->format('Y-m-d')]);
        }

        return $query;
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Builder<\App\Models\Article>  $query
     * @return \Illuminate\Database\Eloquent\Builder<\App\Models\Article>
     */
    public static function applyStartDateOrdering($query, string $key)
    {
        if (! preg_match('/^[a-z][a-z0-9_]*$/', $key)) {
            return $query;
        }

        $path = '$.'.$key;

        return $query
            ->orderByRaw("CASE WHEN JSON_UNQUOTE(JSON_EXTRACT(extra_fields, '{$path}')) IS NULL OR JSON_UNQUOTE(JSON_EXTRACT(extra_fields, '{$path}')) = '' THEN 1 ELSE 0 END")
            ->orderByRaw("JSON_UNQUOTE(JSON_EXTRACT(extra_fields, '{$path}')) ASC")
            ->orderByDesc('is_sticky')
            ->orderByDesc('sort_order')
            ->orderByDesc('id');
    }

    public static function parseFilterDate(?string $value): ?\Illuminate\Support\Carbon
    {
        $value = trim((string) $value);

        if ($value === '' || ! preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
            return null;
        }

        try {
            return \Illuminate\Support\Carbon::createFromFormat('Y-m-d', $value)?->startOfDay();
        } catch (\Throwable) {
            return null;
        }
    }
}
