<?php

declare(strict_types=1);

$url = 'https://www.publicaccountants.org.au/about-ipa/governance/board-of-directors/';
$html = file_get_contents($url, false, stream_context_create([
    'http' => ['header' => "User-Agent: Mozilla/5.0\r\n", 'timeout' => 120],
]));
file_put_contents(__DIR__ . '/board-directors.html', $html);

if (! preg_match('/var pageData = (\{.*?\}); \/\/ comes from/s', $html, $m)) {
    throw new RuntimeException('pageData not found');
}

$data = json_decode($m[1], true);

foreach ($data['result']['components'] ?? [] as $i => $c) {
    echo "[$i] {$c['componentName']}\n";
    if (($c['componentName'] ?? '') === 'copyBlock') {
        $items = $c['componentData']['contentColumnItems'] ?? [];
        $img = '';
        $title = '';
        foreach ($items as $item) {
            if (preg_match('/src=\"([^\"]+)\"/', (string) ($item['description'] ?? ''), $im)) {
                $img = $im[1];
            }
            if (filled($item['title'] ?? '')) {
                $title = strip_tags((string) $item['title']);
            }
        }
        echo "  image: {$img}\n";
        echo "  title: {$title}\n\n";
    }
}

function filled(?string $v): bool
{
    return trim((string) $v) !== '';
}
