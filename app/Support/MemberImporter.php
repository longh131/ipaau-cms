<?php

namespace App\Support;

use App\Models\IpaMember;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class MemberImporter
{
    /** @var array<string, string> */
    private array $columnMap = [];

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

        if (! isset($this->columnMap['member_number'])) {
            throw new \RuntimeException('缺少必需列：持证会员编号');
        }

        $payload = [];
        $skipped = 0;

        foreach ($rows as $row) {
            $memberNumber = trim((string) ($row[$this->columnMap['member_number']] ?? ''));

            if ($memberNumber === '') {
                $skipped++;

                continue;
            }

            $attributes = ['member_number' => $memberNumber];

            foreach ($this->columnMap as $field => $column) {
                if ($field === 'member_number') {
                    continue;
                }

                $attributes[$field] = $this->normalizeValue($field, $row[$column] ?? null);
            }

            $payload[] = $attributes;
        }

        if ($dryRun) {
            return [
                'imported' => count($payload),
                'skipped' => $skipped,
                'dry_run' => true,
            ];
        }

        $imported = 0;

        DB::transaction(function () use ($payload, &$imported): void {
            IpaMember::query()->delete();

            foreach (array_chunk($payload, 200) as $chunk) {
                $now = now();

                foreach ($chunk as &$item) {
                    $item['created_at'] = $now;
                    $item['updated_at'] = $now;
                }
                unset($item);

                IpaMember::query()->insert($chunk);
                $imported += count($chunk);
            }
        });

        return [
            'imported' => $imported,
            'skipped' => $skipped,
            'dry_run' => false,
        ];
    }

    /**
     * @param  array<string, mixed>  $headerRow
     * @return array<string, string>
     */
    private function buildColumnMap(array $headerRow): array
    {
        $map = [];

        foreach ($headerRow as $column => $label) {
            $label = trim((string) $label);

            if ($label === '') {
                continue;
            }

            $field = MemberFieldMap::EXCEL_TO_DB[$label] ?? null;

            if ($field !== null) {
                $map[$field] = $column;
            }
        }

        return $map;
    }

    private function normalizeValue(string $field, mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (in_array($field, MemberFieldMap::dateFields(), true)) {
            return $this->normalizeDate($value);
        }

        return trim((string) $value);
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
