<?php

foreach (['ipa-news-list.html', 'ipa-news-detail.html'] as $file) {
    $html = file_get_contents(__DIR__ . '/' . $file);
    if (! preg_match('/var pageData = (\{.*?\}); \/\/ comes from/s', $html, $m)) {
        continue;
    }
    $data = json_decode($m[1], true);
    echo "=== {$file} ===\n";
    foreach ($data['result']['components'] ?? [] as $i => $c) {
        echo "[$i] {$c['componentName']}\n";
        if (in_array($c['componentName'], ['blogSection', 'articleHeader', 'articleContainer'], true)) {
            echo json_encode($c['componentData'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n";
        }
    }
}
