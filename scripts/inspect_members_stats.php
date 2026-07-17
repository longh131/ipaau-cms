<?php

require __DIR__.'/../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\IReadFilter;

class ChunkReadFilter implements IReadFilter
{
    public function __construct(private int $startRow, private int $endRow) {}

    public function readCell($columnAddress, $row, $worksheetName = ''): bool
    {
        return $row >= $this->startRow && $row <= $this->endRow;
    }
}

$file = __DIR__.'/../bak/会员全数据.xlsx';
$reader = IOFactory::createReader('Xlsx');
$reader->setReadDataOnly(true);

$chunkSize = 500;
$startRow = 1;
$withCert = 0;
$withoutCert = 0;
$levels = [];
$statuses = [];
$genders = [];
$regions = [];
$joinYears = [];

while (true) {
    $filter = new ChunkReadFilter($startRow, $startRow + $chunkSize - 1);
    $reader->setReadFilter($filter);
    $sheet = $reader->load($file)->getActiveSheet();
    $rows = $sheet->toArray(null, true, true, true);

    if ($startRow === 1) {
        $header = array_shift($rows);
        $colCert = 'A';
        $colGender = 'F';
        $colBirth = 'G';
        $colLevel = 'M';
        $colLevelShort = 'N';
        $colStatus = 'L';
        $colRegion = 'AG';
        $colJoin = 'P';
    }

    if ($rows === [] || $rows === null) {
        break;
    }

    $hasData = false;
    foreach ($rows as $row) {
        if ($row === null || ($row['A'] ?? '') === '' && ($row['C'] ?? '') === '') {
            continue;
        }
        $hasData = true;
        $cert = trim((string) ($row[$colCert] ?? ''));
        if ($cert === '') {
            $withoutCert++;

            continue;
        }
        $withCert++;

        $level = trim((string) ($row[$colLevelShort] ?? $row[$colLevel] ?? ''));
        if ($level !== '') {
            $levels[$level] = ($levels[$level] ?? 0) + 1;
        }

        $status = trim((string) ($row[$colStatus] ?? ''));
        if ($status !== '') {
            $statuses[$status] = ($statuses[$status] ?? 0) + 1;
        }

        $gender = trim((string) ($row[$colGender] ?? ''));
        if ($gender !== '') {
            $genders[$gender] = ($genders[$gender] ?? 0) + 1;
        }

        $region = trim((string) ($row[$colRegion] ?? ''));
        if ($region !== '') {
            $regions[$region] = ($regions[$region] ?? 0) + 1;
        }

        $join = $row[$colJoin] ?? '';
        if ($join !== '' && $join !== null) {
            if (is_numeric($join)) {
                $year = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject((float) $join)->format('Y');
            } else {
                $year = substr((string) $join, 0, 4);
            }
            if (preg_match('/^\d{4}$/', $year)) {
                $joinYears[$year] = ($joinYears[$year] ?? 0) + 1;
            }
        }
    }

    if (! $hasData) {
        break;
    }

    $startRow += $chunkSize;
    unset($sheet, $rows);
    gc_collect_cycles();
}

echo "With cert: {$withCert}\n";
echo "Without cert (skipped): {$withoutCert}\n\n";

echo "Levels (short):\n";
arsort($levels);
foreach (array_slice($levels, 0, 15, true) as $k => $v) {
    echo "  {$k}: {$v}\n";
}

echo "\nStatuses:\n";
arsort($statuses);
foreach ($statuses as $k => $v) {
    echo "  {$k}: {$v}\n";
}

echo "\nGenders:\n";
foreach ($genders as $k => $v) {
    echo "  {$k}: {$v}\n";
}

echo "\nTop regions:\n";
arsort($regions);
foreach (array_slice($regions, 0, 10, true) as $k => $v) {
    echo "  {$k}: {$v}\n";
}

echo "\nJoin years:\n";
ksort($joinYears);
foreach (array_slice($joinYears, 0, 10, true) as $k => $v) {
    echo "  {$k}: {$v}\n";
}
