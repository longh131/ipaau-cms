<?php

require dirname(__DIR__) . '/vendor/autoload.php';

$sheet = \PhpOffice\PhpSpreadsheet\IOFactory::load(dirname(__DIR__) . '/bak/IPA_news.xlsx')->getActiveSheet();
$rows = $sheet->toArray(null, true, true, true);
array_shift($rows);

$brackets = 0;
$images = 0;

foreach ($rows as $row) {
    $title = trim((string) ($row['B'] ?? $row['C'] ?? ''));
    if ($title === '') {
        continue;
    }
    if (! str_contains($title, '【') && ! str_contains($title, '】')) {
        continue;
    }
    $brackets++;
    $content = (string) ($row['G'] ?? $row['H'] ?? '');
    preg_match_all('/\bsrc=(["\'])([^"\']+)\1/i', $content, $m);
    $images += count($m[2]);
}

echo 'Bracket titles: ' . $brackets . PHP_EOL;
echo 'Image tags in bracket articles: ' . $images . PHP_EOL;
