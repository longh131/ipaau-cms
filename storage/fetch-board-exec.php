<?php

$url = 'https://www.publicaccountants.org.au/about-ipa/governance/board-executive-committee/';
$html = file_get_contents($url, false, stream_context_create([
    'http' => ['header' => "User-Agent: Mozilla/5.0\r\n", 'timeout' => 120],
]));
file_put_contents(__DIR__ . '/board-exec.html', $html);
echo 'bytes: ' . strlen($html) . PHP_EOL;

if (preg_match('/var pageData = (\{.*?\}); \/\/ comes from/s', $html, $m)) {
    $data = json_decode($m[1], true);
    foreach ($data['result']['components'] ?? [] as $i => $c) {
        echo "[$i] {$c['componentName']}\n";
        if (in_array($c['componentName'] ?? '', ['basicContentWithColumns', 'copyBlock'], true)) {
            echo json_encode($c['componentData'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n";
        }
    }
}
