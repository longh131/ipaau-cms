<?php
$html = file_get_contents('http://ipaau-cms.test/article/read-501', false, stream_context_create([
    'http' => ['header' => "User-Agent: Mozilla/5.0\r\n"],
]));
file_put_contents(__DIR__ . '/read-501.html', $html);
if (preg_match('/body class="([^"]*)"/', $html, $m)) echo "body: {$m[1]}\n";
if (preg_match('/data-type="([^"]*)"/', $html, $m)) echo "first section: {$m[1]}\n";
preg_match_all('/data-type="([^"]*)"/', $html, $types);
echo "sections: " . implode(', ', array_unique($types[1])) . "\n";
