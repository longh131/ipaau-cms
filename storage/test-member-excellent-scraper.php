<?php

require __DIR__.'/../vendor/autoload.php';

$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Support\IpaMemberExcellentPageScraper;

$html = file_get_contents(__DIR__.'/ipaau-excellent-list.html');
$members = IpaMemberExcellentPageScraper::scrapeMembers($html);

echo 'members: '.count($members).PHP_EOL;

if ($members !== []) {
    $first = $members[0];
    echo 'first title: '.$first['title'].PHP_EOL;
    echo 'position: '.str_replace("\n", ' | ', (string) $first['position']).PHP_EOL;
    echo 'summary len: '.strlen((string) $first['summary']).PHP_EOL;
    echo 'image: '.$first['image_url'].PHP_EOL;
    echo 'detail: '.$first['detail_url'].PHP_EOL;
}

$withDetail = count(array_filter($members, fn ($m) => filled($m['detail_url'])));

echo 'with detail url: '.$withDetail.PHP_EOL;
