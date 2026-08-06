<?php

$url = 'https://www.publicaccountants.org.au/news-advocacy/news/';
$html = file_get_contents($url, false, stream_context_create([
    'http' => ['header' => "User-Agent: Mozilla/5.0\r\n", 'timeout' => 120],
]));
file_put_contents(__DIR__ . '/ipa-news-list.html', $html);

$url2 = 'https://www.publicaccountants.org.au/news-advocacy/news/five-defences-in-your-fight-against-cyber-villains/';
$html2 = file_get_contents($url2, false, stream_context_create([
    'http' => ['header' => "User-Agent: Mozilla/5.0\r\n", 'timeout' => 120],
]));
file_put_contents(__DIR__ . '/ipa-news-detail.html', $html2);

if (preg_match('/var pageData = (\{.*?\}); \/\/ comes from/s', $html, $m)) {
    $data = json_decode($m[1], true);
    foreach ($data['result']['components'] ?? [] as $i => $c) {
        echo "LIST [$i] {$c['componentName']}\n";
    }
}

if (preg_match('/var pageData = (\{.*?\}); \/\/ comes from/s', $html2, $m2)) {
    $data2 = json_decode($m2[1], true);
    foreach ($data2['result']['components'] ?? [] as $i => $c) {
        echo "DETAIL [$i] {$c['componentName']}\n";
        if (($c['componentName'] ?? '') === 'heroBanner' || ($c['componentName'] ?? '') === 'copyBlock') {
            echo json_encode($c['componentData'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n";
        }
    }
}
