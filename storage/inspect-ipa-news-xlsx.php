<?php

require dirname(__DIR__) . '/vendor/autoload.php';

$sheet = \PhpOffice\PhpSpreadsheet\IOFactory::load(dirname(__DIR__) . '/bak/IPA_news.xlsx')->getActiveSheet();
$rows = $sheet->toArray(null, true, true, true);
$header = array_shift($rows);

echo 'Headers: ' . json_encode(array_values($header), JSON_UNESCAPED_UNICODE) . PHP_EOL;
echo 'Row count: ' . count($rows) . PHP_EOL;

foreach (array_slice($rows, 0, 3) as $i => $row) {
    echo 'Row ' . ($i + 2) . ': ' . json_encode(array_values($row), JSON_UNESCAPED_UNICODE) . PHP_EOL;
}

$bracketCount = 0;
foreach ($rows as $row) {
    $title = trim((string) reset($row));
    foreach ($row as $v) {
        if (is_string($v) && str_contains($v, 'title')) {
            break;
        }
    }
}
// count titles with 【
$titleCol = null;
foreach ($header as $col => $h) {
    if (strtolower(trim((string)$h)) === 'title') {
        $titleCol = $col;
        break;
    }
}
if ($titleCol) {
    foreach ($rows as $row) {
        $t = (string)($row[$titleCol] ?? '');
        if (str_contains($t, '【') || str_contains($t, '】')) {
            $bracketCount++;
        }
    }
    echo "Titles with brackets: {$bracketCount}\n";
}
