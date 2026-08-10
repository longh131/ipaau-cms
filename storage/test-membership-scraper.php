<?php

require __DIR__.'/../vendor/autoload.php';

$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Support\IpaMembershipArticlePageScraper;

$listHtml = file_get_contents(__DIR__.'/ipaau-pointofview-list.html');
$items = IpaMembershipArticlePageScraper::scrapeListItems($listHtml);
echo 'pointofview list: '.count($items).PHP_EOL;
echo 'max page: '.IpaMembershipArticlePageScraper::scrapeMaxPageNumber($listHtml).PHP_EOL;

$detailHtml = file_get_contents(__DIR__.'/ipaau-interview-detail-110.html');
$detail = IpaMembershipArticlePageScraper::scrapeDetail($detailHtml);
echo 'detail title: '.$detail['title'].PHP_EOL;
echo 'content length: '.strlen($detail['content_html']).PHP_EOL;
echo substr($detail['content_html'], 0, 200).PHP_EOL;
