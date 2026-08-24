<?php

namespace App\Services;

use App\Models\Course;
use App\Models\CpeMember;
use App\Models\IpaMember;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class CpeMemberService
{
    /**
     * @return array<int, string>
     */
    public function parseMemberNumbers(string $input): array
    {
        $parts = preg_split('/[\s,，;；\r\n]+/', $input) ?: [];
        $numbers = [];

        foreach ($parts as $part) {
            $number = CpeMember::normalizeMemberNumber($part);

            if ($number !== null) {
                $numbers[$number] = $number;
            }
        }

        return array_values($numbers);
    }

    /**
     * @return array{created: int, updated: int, skipped: int}
     */
    public function bulkAdd(int $courseId, string $memberNumbersInput, int $attend = CpeMember::ATTEND_REGISTERED): array
    {
        $course = Course::query()->findOrFail($courseId);
        $memberNumbers = $this->parseMemberNumbers($memberNumbersInput);

        if ($memberNumbers === []) {
            throw new \InvalidArgumentException('请至少输入一个有效的会员号。');
        }

        $created = 0;
        $updated = 0;

        foreach ($memberNumbers as $memberNumber) {
            $existing = CpeMember::query()
                ->where('course_id', $course->getKey())
                ->where('member_number', $memberNumber)
                ->first();

            if ($existing !== null) {
                $updated++;

                continue;
            }

            CpeMember::query()->create([
                ...CpeMember::syncCourseLink($course),
                'member_number' => $memberNumber,
                'attend' => CpeMember::normalizeAttend($attend),
                'registration_method' => '后台批量添加',
            ]);

            $created++;
        }

        return [
            'created' => $created,
            'updated' => $updated,
            'skipped' => count($memberNumbers) - $created - $updated,
        ];
    }

    /**
     * @return array{updated: int, missing: int}
     */
    public function bulkUpdate(int $courseId, string $memberNumbersInput, int $attend): array
    {
        $course = Course::query()->findOrFail($courseId);
        $memberNumbers = $this->parseMemberNumbers($memberNumbersInput);

        if ($memberNumbers === []) {
            throw new \InvalidArgumentException('请至少输入一个有效的会员号。');
        }

        $updated = $this->queryForCourse($course)
            ->whereIn('member_number', $memberNumbers)
            ->update(['attend' => CpeMember::normalizeAttend($attend)]);

        return [
            'updated' => $updated,
            'missing' => count($memberNumbers) - $updated,
        ];
    }

    /**
     * @return array{
     *     registered: Collection<int, CpeMember>,
     *     present: Collection<int, CpeMember>,
     *     absent: Collection<int, CpeMember>
     * }
     */
    public function groupedForCourse(Course $course): array
    {
        $records = $this->queryForCourse($course)
            ->with('member')
            ->orderBy('member_number')
            ->get();

        return [
            'registered' => $records->where('attend', CpeMember::ATTEND_REGISTERED)->values(),
            'present' => $records->where('attend', CpeMember::ATTEND_PRESENT)->values(),
            'absent' => $records->where('attend', CpeMember::ATTEND_ABSENT)->values(),
        ];
    }

    /**
     * @return Builder<CpeMember>
     */
    public function queryForCourse(Course $course): Builder
    {
        return CpeMember::query()->where(function (Builder $query) use ($course): void {
            $query->where('course_id', $course->getKey());

            if ($course->legacy_id !== null) {
                $query->orWhere(function (Builder $nested) use ($course): void {
                    $nested->whereNull('course_id')
                        ->where('book_legacy_id', $course->legacy_id);
                });
            }
        });
    }

    /**
     * @return array{
     *     session_count: int,
     *     total_credits: float,
     *     records: Collection<int, array<string, mixed>>
     * }
     */
    public function memberSessionsInRange(string $memberNumber, Carbon $from, Carbon $to, bool $presentOnly = true): array
    {
        $query = CpeMember::query()
            ->where('member_number', $memberNumber)
            ->when($presentOnly, fn (Builder $builder) => $builder->where('attend', CpeMember::ATTEND_PRESENT))
            ->where(function (Builder $builder): void {
                $builder->whereNotNull('course_id')
                    ->orWhereNotNull('book_legacy_id');
            });

        $records = $query->get();

        $courseIds = $records->pluck('course_id')->filter()->unique()->values()->all();
        $legacyIds = $records->whereNull('course_id')->pluck('book_legacy_id')->filter()->unique()->values()->all();

        $coursesById = Course::query()->whereIn('id', $courseIds)->get()->keyBy('id');
        $coursesByLegacy = Course::query()->whereIn('legacy_id', $legacyIds)->get()->keyBy('legacy_id');

        $items = collect();

        foreach ($records as $record) {
            $course = $record->course_id
                ? $coursesById->get($record->course_id)
                : $coursesByLegacy->get($record->book_legacy_id);

            if ($course === null || $course->starts_at === null) {
                continue;
            }

            if ($course->starts_at->lt($from) || $course->starts_at->gt($to)) {
                continue;
            }

            $items->push([
                'course' => $course,
                'record' => $record,
                'starts_at' => $course->starts_at,
                'title' => $course->title,
                'city' => $course->city,
                'cpd_credits' => $course->formattedCpdCredits(),
                'attend_label' => $record->attendLabel(),
            ]);
        }

        $items = $items->sortBy(fn (array $item) => $item['starts_at']?->timestamp ?? 0)->values();

        $totalCredits = $items->sum(fn (array $item): float => (float) ($item['course']->cpd_credits ?? 0));

        return [
            'session_count' => $items->count(),
            'total_credits' => round($totalCredits, 1),
            'records' => $items,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function registrationRow(CpeMember $record, int $index): array
    {
        $member = $record->member;

        return [
            'index' => $index,
            'member_number' => $record->member_number,
            'full_name' => $member?->full_name ?: '—',
            'gender' => $member?->gender ?: '—',
            'email' => $member?->email ?: '—',
            'work_phone' => $member?->work_phone ?: '',
            'home_phone' => $member?->home_phone ?: '',
            'mobile_phone' => $member?->mobile_phone ?: '',
            'registration_method' => $record->registration_method ?: '—',
        ];
    }
}
