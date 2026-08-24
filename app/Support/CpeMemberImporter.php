<?php

namespace App\Support;

use App\Models\CpeMember;
use App\Models\Course;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;

class CpeMemberImporter
{
    /** @var array<string, string> */
    private const COLUMN_ALIASES = [
        'ipa_book_id' => 'ipa_book_id',
        'book_id' => 'ipa_book_id',
        'book_legacy_id' => 'ipa_book_id',
        'id' => 'ipa_book_id',
        'member_number' => 'member_number',
        'member_no' => 'member_number',
        'membernumber' => 'member_number',
        '会员号' => 'member_number',
        '持证会员编号' => 'member_number',
        'attend' => 'attend',
        '出席' => 'attend',
        'registration_method' => 'registration_method',
        '报名方式' => 'registration_method',
        'reg_type' => 'registration_method',
        'baoming' => 'registration_method',
        'method' => 'registration_method',
    ];

    /** @var array<string, string> */
    private array $columnMap = [];

    public function import(string $filePath, bool $truncate = true): array
    {
        if (! is_file($filePath)) {
            throw new \InvalidArgumentException("文件不存在：{$filePath}");
        }

        ini_set('memory_limit', '2048M');

        $sheet = IOFactory::load($filePath)->getActiveSheet();
        $rows = $sheet->toArray(null, true, true, true);
        $headerRow = array_shift($rows);

        if ($headerRow === null) {
            throw new \RuntimeException('Excel 文件为空。');
        }

        $this->columnMap = $this->buildColumnMap($headerRow);

        if (! isset($this->columnMap['member_number'])) {
            throw new \RuntimeException('Excel 缺少会员号列（member_number / 会员号 / 持证会员编号）。');
        }

        if (! isset($this->columnMap['ipa_book_id'])) {
            throw new \RuntimeException('Excel 缺少活动 ID 列（ipa_book_id / book_id / ID）。');
        }

        $legacyCourseMap = Course::query()
            ->whereNotNull('legacy_id')
            ->pluck('id', 'legacy_id')
            ->map(fn ($id): int => (int) $id)
            ->all();

        $payload = [];
        $skippedEmpty = 0;
        $skippedInvalid = 0;
        $unresolvedCourses = 0;

        foreach ($rows as $row) {
            $memberNumber = CpeMember::normalizeMemberNumber($row[$this->columnMap['member_number']] ?? null);
            $bookLegacyId = $this->normalizeLegacyId($row[$this->columnMap['ipa_book_id']] ?? null);

            if ($memberNumber === null || $bookLegacyId === null) {
                $skippedEmpty++;

                continue;
            }

            $courseId = $legacyCourseMap[$bookLegacyId] ?? null;

            if ($courseId === null) {
                $unresolvedCourses++;
            }

            $attend = CpeMember::normalizeAttend($row[$this->columnMap['attend']] ?? null);
            $registrationMethod = isset($this->columnMap['registration_method'])
                ? $this->normalizeText($row[$this->columnMap['registration_method']] ?? null)
                : null;

            $dedupeKey = ($courseId ?? 'legacy:'.$bookLegacyId).':'.$memberNumber;

            $payload[$dedupeKey] = [
                'course_id' => $courseId,
                'book_legacy_id' => $bookLegacyId,
                'member_number' => $memberNumber,
                'attend' => $attend,
                'registration_method' => $registrationMethod,
            ];
        }

        DB::transaction(function () use ($truncate, $payload): void {
            if ($truncate) {
                CpeMember::query()->delete();
            }

            foreach ($payload as $row) {
                if ($row['course_id'] !== null) {
                    CpeMember::query()->updateOrCreate(
                        [
                            'course_id' => $row['course_id'],
                            'member_number' => $row['member_number'],
                        ],
                        $row,
                    );

                    continue;
                }

                CpeMember::query()->updateOrCreate(
                    [
                        'book_legacy_id' => $row['book_legacy_id'],
                        'member_number' => $row['member_number'],
                    ],
                    $row,
                );
            }
        });

        return [
            'imported' => count($payload),
            'skipped_empty' => $skippedEmpty,
            'skipped_invalid' => $skippedInvalid,
            'unresolved_courses' => $unresolvedCourses,
        ];
    }

    private function normalizeHeader(string $label): string
    {
        $label = trim($label);

        if ($label === '') {
            return '';
        }

        $lower = mb_strtolower($label);

        return str_replace([' ', '_', '-'], '', $lower);
    }

    /**
     * @return array<string, string>
     */
    private function aliasLookupKey(string $alias): string
    {
        return str_replace([' ', '_', '-'], '', mb_strtolower($alias));
    }

    /**
     * @param  array<int|string, mixed>  $headerRow
     * @return array<string, string>
     */
    private function buildColumnMap(array $headerRow): array
    {
        $aliasLookup = [];

        foreach (self::COLUMN_ALIASES as $alias => $field) {
            $aliasLookup[$this->aliasLookupKey($alias)] = $field;
        }

        $map = [];

        foreach ($headerRow as $columnKey => $label) {
            $normalized = $this->normalizeHeader((string) $label);

            if ($normalized === '') {
                continue;
            }

            if (isset($aliasLookup[$normalized])) {
                $map[$aliasLookup[$normalized]] = (string) $columnKey;
            }
        }

        return $map;
    }

    private function normalizeLegacyId(mixed $value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_numeric($value)) {
            $id = (int) $value;

            return $id > 0 ? $id : null;
        }

        return null;
    }

    private function normalizeText(mixed $value): ?string
    {
        $text = trim((string) ($value ?? ''));

        return $text !== '' ? $text : null;
    }
}
