<?php

namespace App\Support;

use App\Models\Category;
use Filament\Forms;
use Filament\Schemas\Components\Utilities\Get;

class ArticleExtraFields
{
    /** @var array<string, string> */
    public const TYPE_OPTIONS = [
        'text' => '单行文本',
        'textarea' => '多行文本',
        'url' => '链接',
        'image' => '图片路径',
    ];

    /**
     * @param  array<int, mixed>|null  $schema
     * @return array<int, array{key: string, label: string, type: string, show_in_list: bool}>
     */
    public static function normalizeSchema(?array $schema): array
    {
        if (! is_array($schema)) {
            return [];
        }

        $normalized = [];

        foreach ($schema as $field) {
            if (! is_array($field)) {
                continue;
            }

            $key = trim((string) ($field['key'] ?? ''));

            if ($key === '' || ! preg_match('/^[a-z][a-z0-9_]*$/', $key)) {
                continue;
            }

            $type = (string) ($field['type'] ?? 'text');

            if (! array_key_exists($type, self::TYPE_OPTIONS)) {
                $type = 'text';
            }

            $normalized[] = [
                'key' => $key,
                'label' => trim((string) ($field['label'] ?? $key)),
                'type' => $type,
                'show_in_list' => (bool) ($field['show_in_list'] ?? false),
            ];
        }

        return $normalized;
    }

    /**
     * @return array<int, Forms\Components\Component>
     */
    public static function categorySchemaRepeaterFields(): array
    {
        return [
            Forms\Components\TextInput::make('key')
                ->label('字段键名')
                ->helperText('英文小写 + 下划线，如 featured_image、tags')
                ->required()
                ->maxLength(64)
                ->regex('/^[a-z][a-z0-9_]*$/')
                ->columnSpan(1),
            Forms\Components\TextInput::make('label')
                ->label('字段标签')
                ->required()
                ->maxLength(120)
                ->columnSpan(1),
            Forms\Components\Select::make('type')
                ->label('字段类型')
                ->options(self::TYPE_OPTIONS)
                ->default('text')
                ->required()
                ->columnSpan(1),
            Forms\Components\Toggle::make('show_in_list')
                ->label('列表页显示')
                ->default(false)
                ->columnSpan(1),
        ];
    }

    /**
     * @return array<int, Forms\Components\Component>
     */
    public static function articleFormComponents(?Category $category): array
    {
        $schema = self::normalizeSchema($category?->article_extra_field_schema);

        if ($schema === []) {
            return [];
        }

        $components = [];

        foreach ($schema as $field) {
            $components[] = self::formComponent($field);
        }

        return $components;
    }

    /**
     * @param  array{key: string, label: string, type: string, show_in_list: bool}  $field
     */
    protected static function formComponent(array $field): Forms\Components\Component
    {
        $key = $field['key'];
        $label = filled($field['label']) ? $field['label'] : $key;

        return match ($field['type']) {
            'textarea' => Forms\Components\Textarea::make($key)
                ->label($label)
                ->rows(3)
                ->columnSpanFull(),
            'url' => Forms\Components\TextInput::make($key)
                ->label($label)
                ->url()
                ->maxLength(2048)
                ->columnSpanFull(),
            'image' => Forms\Components\TextInput::make($key)
                ->label($label)
                ->helperText('相对路径（如 assets/img/...）或完整 URL')
                ->maxLength(2048)
                ->columnSpanFull(),
            default => Forms\Components\TextInput::make($key)
                ->label($label)
                ->maxLength(500)
                ->columnSpanFull(),
        };
    }

    /**
     * @param  array<string, mixed>|null  $extraFields
     * @param  array<int, array{key: string, label: string, type: string, show_in_list: bool}>  $schema
     * @return array<string, mixed>
     */
    public static function normalizeValuesForStorage(?array $extraFields, array $schema): array
    {
        $extraFields = is_array($extraFields) ? $extraFields : [];
        $allowedKeys = array_column($schema, 'key');
        $normalized = [];

        foreach ($allowedKeys as $key) {
            if (! array_key_exists($key, $extraFields)) {
                continue;
            }

            $value = $extraFields[$key];

            if (is_string($value)) {
                $value = trim($value);
            }

            if ($value === '' || $value === null) {
                continue;
            }

            $normalized[$key] = $value;
        }

        return $normalized;
    }

    /**
     * @param  array<string, mixed>|null  $extraFields
     * @param  array<int, array{key: string, label: string, type: string, show_in_list: bool}>  $schema
     * @return array<int, array{key: string, label: string, type: string, value: mixed}>
     */
    public static function forFrontend(?array $extraFields, ?array $schema): array
    {
        $extraFields = is_array($extraFields) ? $extraFields : [];
        $schema = self::normalizeSchema($schema);
        $items = [];

        foreach ($schema as $field) {
            $value = $extraFields[$field['key']] ?? null;

            if ($value === null || $value === '') {
                continue;
            }

            $items[] = [
                'key' => $field['key'],
                'label' => $field['label'],
                'type' => $field['type'],
                'value' => $value,
            ];
        }

        return $items;
    }

    /**
     * @param  array<string, mixed>|null  $extraFields
     * @param  array<int, array{key: string, label: string, type: string, show_in_list: bool}>  $schema
     */
    public static function listValue(?array $extraFields, array $schema, string $key): mixed
    {
        $extraFields = is_array($extraFields) ? $extraFields : [];

        return $extraFields[$key] ?? null;
    }

    /**
     * @param  array<int, mixed>|null  $schema
     * @return array<int, array{key: string, label: string, type: string, show_in_list: bool}>
     */
    public static function listFields(?array $schema): array
    {
        return array_values(array_filter(
            self::normalizeSchema($schema),
            fn (array $field): bool => $field['show_in_list'],
        ));
    }

    /**
     * @param  array<int, array{key: string, label: string, type: string, show_in_list: bool}>  $listFields
     */
    public static function listImageUrl(?array $extraFields, array $listFields): ?string
    {
        foreach ($listFields as $field) {
            if ($field['type'] !== 'image') {
                continue;
            }

            $url = self::assetUrl(self::listValue($extraFields, $listFields, $field['key']));

            if ($url) {
                return $url;
            }
        }

        return null;
    }

    /**
     * @param  array<int, array{key: string, label: string, type: string, show_in_list: bool}>  $listFields
     * @return array<int, string>
     */
    public static function listTags(?array $extraFields, array $listFields): array
    {
        foreach ($listFields as $field) {
            if ($field['key'] !== 'tags') {
                continue;
            }

            $value = self::listValue($extraFields, $listFields, $field['key']);

            if (! is_string($value) || blank($value)) {
                return [];
            }

            return array_values(array_filter(array_map('trim', preg_split('/\s*,\s*/', $value) ?: [])));
        }

        return [];
    }

    public static function assetUrl(mixed $value): ?string
    {
        if (! is_string($value) || blank($value)) {
            return null;
        }

        $value = trim($value);

        if (str_starts_with($value, 'http://') || str_starts_with($value, 'https://') || str_starts_with($value, '//')) {
            return $value;
        }

        return asset(ltrim($value, '/'));
    }
}
