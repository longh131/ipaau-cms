<?php

$html = file_get_contents(__DIR__ . '/ipaau-video-page.html');

function extractSection(string $html, string $id): string
{
    if (! preg_match('/<div class="videoList" id="' . preg_quote($id, '/') . '">(.*?)<div class="videoList" id="/s', $html, $m)) {
        if (! preg_match('/<div class="videoList" id="' . preg_quote($id, '/') . '">(.*?)<div class="clearfix"><\/div>\s*<\/div>\s*<div class="videoList"/s', $html, $m)) {
            return '';
        }
    }

    return $m[1];
}

function parseItems(string $section): array
{
    $items = [];
    preg_match_all('/<div class="listSingle">\s*(.*?)\s*<\/div>/s', $section, $blocks);

    foreach ($blocks[1] as $block) {
        $title = trim(preg_replace('/\s+/u', ' ', strip_tags($block)) ?? '');
        $videoFile = null;
        $coverPath = null;

        if (preg_match("/src=([^'\"&]+\.mp4)/i", $block, $m)) {
            $videoFile = urldecode($m[1]);
        } elseif (preg_match("/href=['\"]([^'\"]+\.mp4)['\"]/i", $block, $m)) {
            $videoFile = urldecode($m[1]);
        }

        if (preg_match('/<img[^>]+src=["\']([^"\']+)["\']/i', $block, $m)) {
            $coverPath = html_entity_decode($m[1]);
        }

        if ($title === '' || $videoFile === null) {
            continue;
        }

        $items[] = compact('title', 'videoFile', 'coverPath');
    }

    return $items;
}

foreach (['media' => 'IPA播报', 'function' => 'IPA活动回顾'] as $id => $label) {
    $section = extractSection($html, $id);
    $items = parseItems($section);
    echo $label . ' (' . $id . '): ' . count($items) . PHP_EOL;
    foreach (array_slice($items, 0, 3) as $item) {
        echo '  ' . $item['title'] . ' | ' . $item['videoFile'] . PHP_EOL;
    }
}
