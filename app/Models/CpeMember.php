<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CpeMember extends Model
{
    public const ATTEND_ABSENT = 0;

    public const ATTEND_PRESENT = 1;

    public const ATTEND_REGISTERED = 2;

    /** @var array<int, string> */
    public const ATTEND_OPTIONS = [
        self::ATTEND_ABSENT => '缺席',
        self::ATTEND_PRESENT => '到场',
        self::ATTEND_REGISTERED => '已报名',
    ];

    protected $fillable = [
        'course_id',
        'book_legacy_id',
        'member_number',
        'attend',
        'registration_method',
    ];

    protected $casts = [
        'book_legacy_id' => 'integer',
        'attend' => 'integer',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(IpaMember::class, 'member_number', 'member_number');
    }

    public function attendLabel(): string
    {
        return self::ATTEND_OPTIONS[$this->attend] ?? (string) $this->attend;
    }

    public static function normalizeMemberNumber(mixed $value): ?string
    {
        $number = trim((string) $value);

        if ($number === '') {
            return null;
        }

        if (is_numeric($number)) {
            return (string) (int) $number;
        }

        return $number;
    }

    public static function normalizeAttend(mixed $value, int $default = self::ATTEND_REGISTERED): int
    {
        if ($value === null || $value === '') {
            return $default;
        }

        $attend = (int) $value;

        return array_key_exists($attend, self::ATTEND_OPTIONS) ? $attend : $default;
    }

    public static function resolveCourseId(?int $bookLegacyId, ?int $courseId = null): ?int
    {
        if ($courseId !== null && $courseId > 0) {
            return $courseId;
        }

        if ($bookLegacyId === null || $bookLegacyId <= 0) {
            return null;
        }

        $resolved = Course::query()
            ->where('legacy_id', $bookLegacyId)
            ->value('id');

        return $resolved !== null ? (int) $resolved : null;
    }

    public static function syncCourseLink(Course $course): array
    {
        return [
            'course_id' => $course->getKey(),
            'book_legacy_id' => $course->legacy_id,
        ];
    }
}
