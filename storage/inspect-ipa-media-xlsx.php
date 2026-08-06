<?php

require dirname(__DIR__) . '/vendor/autoload.php';

$sheet = \PhpOffice\PhpSpreadsheet\IOFactory::load(dirname(__DIR__) . '/bak/IPA_media.xlsx')->getActiveSheet();
$rows = $sheet->toArray(null, true, true, true);
$header = array_shift($rows);

echo 'Headers: ' . json_encode(array_values($header), JSON_UNESCAPED_UNICODE) . PHP_EOL;
echo 'Row count: ' . count($rows) . PHP_EOL;

$withImages = 0;
$totalImages = 0;
$sampleUrls = [];

foreach ($rows as $i => $row) {
    $content = (string) ($row['D'] ?? '');
    preg_match_all('/\bsrc=(["\'])([^"\']+)\1/i', $content, $matches);
    $count = count($matches[2]);
    if ($count > 0) {
        $withImages++;
        $totalImages += $count;
        if (count($sampleUrls) < 5) {
            $sampleUrls[] = ['row' => $i + 2, 'id' => $row['A'] ?? '', 'url' => $matches[2][0]];
        }
    }
}

echo 'Rows with images: ' . $withImages . PHP_EOL;
echo 'Total image tags: ' . $totalImages . PHP_EOL;
foreach ($sampleUrls as $s) {
    echo 'Sample row ' . $s['row'] . ' id=' . $s['id'] . ' url=' . $s['url'] . PHP_EOL;
}

// show one row content snippet
foreach ($rows as $i => $row) {
    $content = (string) ($row['D'] ?? '');
    if (str_contains($content, 'img') || str_contains($content, 'upload')) {
        echo 'First img row ' . ($i + 2) . ' snippet: ' . substr($content, 0, 500) . PHP_EOL;
        break;
    }
}
