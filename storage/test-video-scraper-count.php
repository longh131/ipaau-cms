<?php

$html = file_get_contents(__DIR__ . '/ipaau-video-page.html');

preg_match('/<div class="videoList" id="media">(.*?)<div class="videoList" id="class">/s', $html, $m);
$section = $m[1] ?? '';
preg_match_all('/<div class="listSingle">\s*(.*?)\s*<\/div>/s', $section, $blocks);
echo 'Total listSingle in media: ' . count($blocks[1]) . PHP_EOL;

$mp4 = 0;
$skipped = [];
foreach ($blocks[1] as $block) {
    if (preg_match("/\.mp4|\.mkv/i", $block)) {
        $mp4++;
    } else {
        $skipped[] = trim(preg_replace('/\s+/u', ' ', strip_tags($block)) ?? '');
    }
}
echo "With mp4/mkv: $mp4" . PHP_EOL;
echo 'Skipped (' . count($skipped) . '):' . PHP_EOL;
foreach (array_slice($skipped, 0, 10) as $s) {
    echo '  ' . mb_substr($s, 0, 60) . PHP_EOL;
}
