<?php

require __DIR__.'/../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\IReadFilter;

class FirstRowsFilter implements IReadFilter
{
    public function __construct(private int $maxRow) {}

    public function readCell($columnAddress, $row, $worksheetName = ''): bool
    {
        return $row <= $this->maxRow;
    }
}

$file = __DIR__.'/../bak/会员全数据.xlsx';
$reader = IOFactory::createReader('Xlsx');
$reader->setReadDataOnly(true);
$reader->setReadFilter(new FirstRowsFilter(5));
$sheet = $reader->load($file)->getActiveSheet();
$rows = $sheet->toArray(null, true, true, true);
$header = array_shift($rows);

echo 'COLUMNS: '.count($header)."\n";
foreach ($header as $col => $label) {
    echo "{$col}\t{$label}\n";
}

echo "\nSAMPLE ROW 1:\n";
if (! empty($rows)) {
    $row = reset($rows);
    foreach ($header as $col => $label) {
        $val = $row[$col] ?? '';
        if ($val !== '' && $val !== null) {
            echo "{$label}: {$val}\n";
        }
    }
}
