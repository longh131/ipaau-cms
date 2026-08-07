<?php

require dirname(__DIR__) . '/vendor/autoload.php';
$app = require dirname(__DIR__) . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Article;

$keywords = ['中文直播', '中文线下', '英文线上', '英文在线', '公开课', '在线', '线下', '直播'];
$counts = array_fill_keys($keywords, 0);

foreach (Article::query()->where('category_id', 69)->pluck('title') as $title) {
    foreach ($keywords as $kw) {
        if (str_contains($title, $kw)) {
            $counts[$kw]++;
        }
    }
}

print_r($counts);

echo PHP_EOL . 'Samples:' . PHP_EOL;
foreach (['中文直播', '中文线下', '英文在线', '英文线上', '公开课'] as $kw) {
    $sample = Article::query()->where('category_id', 69)->where('title', 'like', '%'.$kw.'%')->value('title');
    if ($sample) {
        echo $kw . ': ' . $sample . PHP_EOL;
    }
}
