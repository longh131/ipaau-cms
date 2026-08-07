<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Course extends Model
{
    public const CATEGORY_CHINESE_LIVE = 48;

    public const CATEGORY_CHINESE_OFFLINE = 49;

    public const CATEGORY_ENGLISH_ONLINE = 50;

    public const CATEGORY_PUBLIC = 54;

    /** @var array<int, string> */
    public const CATEGORY_OPTIONS = [
        self::CATEGORY_CHINESE_LIVE => '中文直播',
        self::CATEGORY_CHINESE_OFFLINE => '中文线下',
        self::CATEGORY_ENGLISH_ONLINE => '英文线上',
        self::CATEGORY_PUBLIC => '公开课',
    ];

    /** @return array<int> */
    public static function categoryIds(): array
    {
        return array_keys(self::CATEGORY_OPTIONS);
    }

    public static function isCourseCategory(int|string|null $categoryId): bool
    {
        if ($categoryId === null || $categoryId === '') {
            return false;
        }

        return in_array((int) $categoryId, self::categoryIds(), true);
    }

    protected $fillable = [
        'legacy_id',
        'category_id',
        'title',
        'slug',
        'article_url',
        'is_active',
        'legacy_added_at',
        'registration_url',
        'content',
        'city',
        'starts_at',
        'registration_deadline',
        'cpd_credits',
        'price',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'legacy_added_at' => 'date',
        'starts_at' => 'date',
        'registration_deadline' => 'date',
        'cpd_credits' => 'decimal:2',
        'sort_order' => 'integer',
        'legacy_id' => 'integer',
    ];

    protected $attributes = [
        'is_active' => true,
        'sort_order' => 0,
    ];

    protected static function booted(): void
    {
        static::created(function (Course $course): void {
            if ((int) $course->sort_order !== 0) {
                return;
            }

            $course->forceFill([
                'sort_order' => $course->getKey(),
            ])->saveQuietly();
        });
    }

    /**
     * 新建课程时「排序」字段的预填值（预计下一门课程 ID）。
     */
    public static function defaultSortOrderForNew(): int
    {
        return (int) static::query()->max('id') + 1;
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function isRegistrationDeadlinePassed(): bool
    {
        if ($this->registration_deadline === null) {
            return false;
        }

        return now()->toDateString() > $this->registration_deadline->toDateString();
    }

    public function isRegistrationOpen(): bool
    {
        return $this->is_active && ! $this->isRegistrationDeadlinePassed();
    }

    public function canRegisterOnline(): bool
    {
        return $this->isRegistrationOpen() && filled($this->registration_url);
    }

    public function registrationStatusLabel(): string
    {
        return $this->isRegistrationOpen() ? '报名' : '报名已结束';
    }

    public function formattedCpdCredits(): ?string
    {
        if ($this->cpd_credits === null) {
            return null;
        }

        return self::formatCpdCredits($this->cpd_credits);
    }

    public static function normalizeCpdCredits(mixed $value): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }

        return round((float) $value, 1);
    }

    public static function formatCpdCredits(mixed $value): ?string
    {
        $normalized = self::normalizeCpdCredits($value);

        if ($normalized === null) {
            return null;
        }

        return rtrim(rtrim(number_format($normalized, 1, '.', ''), '0'), '.');
    }
}
