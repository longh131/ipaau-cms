<?php

namespace App\Support;

use App\Models\Course;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class CourseImporter
{
    /** @var array<string, string> */
    private const EXCEL_COLUMNS = [
        'legacy_id' => 'ID',
        'title' => 'title',
        'article_url' => 'ttfrom',
        'is_active' => 'run',
        'legacy_added_at' => 'addtime',
        'registration_url' => 'url',
        'content' => 'content',
        'city' => 'city',
        'starts_at' => 'acttime',
        'registration_deadline' => 'endtime',
        'cpd_credits' => 'cpe_credit',
        'price' => 'price',
    ];

    /** @var array<string, string> */
    private array $columnMap = [];

    /** @var array<string, bool> */
    private array $usedSlugs = [];

    public function import(string $filePath, bool $dryRun = false): array
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

        if (! isset($this->columnMap['title'])) {
            throw new \RuntimeException('缺少必需列：title');
        }

        $payload = [];
        $skippedNoBracket = 0;
        $skippedEmpty = 0;

        foreach ($rows as $row) {
            $title = $this->normalizeText($row[$this->columnMap['title']] ?? null);

            if ($title === '') {
                $skippedEmpty++;

                continue;
            }

            $categoryId = CourseCategoryClassifier::resolve($title);

            if ($categoryId === null) {
                $skippedNoBracket++;

                continue;
            }

            $legacyId = $this->normalizeLegacyId($row[$this->columnMap['legacy_id']] ?? null);
            $slugBase = CourseSlug::fromTitle($title.($legacyId ? '-'.$legacyId : ''));
            $slug = $this->reserveSlug($slugBase);

            $payload[] = [
                'legacy_id' => $legacyId,
                'category_id' => $categoryId,
                'title' => $title,
                'slug' => $slug,
                'article_url' => $this->normalizeText($row[$this->columnMap['article_url']] ?? null) ?: null,
                'is_active' => $this->normalizeBoolean($row[$this->columnMap['is_active']] ?? null),
                'legacy_added_at' => $this->normalizeDate($row[$this->columnMap['legacy_added_at']] ?? null),
                'registration_url' => $this->normalizeText($row[$this->columnMap['registration_url']] ?? null) ?: null,
                'content' => $this->normalizeContent($row[$this->columnMap['content']] ?? null),
                'city' => $this->normalizeText($row[$this->columnMap['city']] ?? null) ?: null,
                'starts_at' => $this->normalizeDate($row[$this->columnMap['starts_at']] ?? null),
                'registration_deadline' => $this->normalizeDate($row[$this->columnMap['registration_deadline']] ?? null),
                'cpd_credits' => $this->normalizeDecimal($row[$this->columnMap['cpd_credits']] ?? null),
                'price' => $this->normalizePrice($row[$this->columnMap['price']] ?? null),
                'sort_order' => 0,
            ];
        }

        if ($dryRun) {
            return [
                'imported' => count($payload),
                'skipped_no_bracket' => $skippedNoBracket,
                'skipped_empty' => $skippedEmpty,
                'dry_run' => true,
            ];
        }

        $imported = 0;

        DB::transaction(function () use ($payload, &$imported): void {
            Course::query()->delete();

            foreach (array_chunk($payload, 100) as $chunk) {
                $now = now();

                foreach ($chunk as &$item) {
                    $item['created_at'] = $now;
                    $item['updated_at'] = $now;
                    $item['is_active'] = $item['is_active'] ? 1 : 0;
                }
                unset($item);

                Course::query()->insert($chunk);
                $imported += count($chunk);
            }
        });

        return [
            'imported' => $imported,
            'skipped_no_bracket' => $skippedNoBracket,
            'skipped_empty' => $skippedEmpty,
            'dry_run' => false,
        ];
    }

    /**
     * @param  array<string, mixed>  $headerRow
     * @return array<string, string>
     */
    private function buildColumnMap(array $headerRow): array
    {
        $labelToField = array_flip(self::EXCEL_COLUMNS);
        $map = [];

        foreach ($headerRow as $column => $label) {
            $label = trim((string) $label);

            if ($label === '') {
                continue;
            }

            $field = $labelToField[$label] ?? null;

            if ($field !== null) {
                $map[$field] = $column;
            }
        }

        return $map;
    }

    private function reserveSlug(string $slug): string
    {
        $candidate = $slug;
        $suffix = 2;

        while (isset($this->usedSlugs[$candidate])) {
            $candidate = $slug.'-'.$suffix;
            $suffix++;
        }

        $this->usedSlugs[$candidate] = true;

        return $candidate;
    }

    private function normalizeText(mixed $value): string
    {
        if ($value === null || $value === '') {
            return '';
        }

        return trim(html_entity_decode((string) $value, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
    }

    private function normalizeContent(mixed $value): ?string
    {
        $content = $this->normalizeText($value);

        return $content === '' ? null : $content;
    }

    private function normalizeLegacyId(mixed $value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        return is_numeric($value) ? (int) $value : null;
    }

    private function normalizeBoolean(mixed $value): bool
    {
        if ($value === null || $value === '') {
            return false;
        }

        return in_array(strtolower(trim((string) $value)), ['1', 'true', 'yes', 'y', '是', '开通'], true);
    }

    private function normalizeDecimal(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (! is_numeric($value)) {
            return null;
        }

        return number_format((float) $value, 2, '.', '');
    }

    private function normalizePrice(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        $price = trim((string) $value);

        return $price === '' ? null : $price;
    }

    private function normalizeDate(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        try {
            if (is_numeric($value)) {
                return ExcelDate::excelToDateTimeObject((float) $value)->format('Y-m-d');
            }

            return Carbon::parse((string) $value)->format('Y-m-d');
        } catch (\Throwable) {
            return null;
        }
    }
}
