<?php

require dirname(__DIR__) . '/vendor/autoload.php';
$app = require dirname(__DIR__) . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Article;

foreach (Article::query()->where('category_id', 69)->where('title', 'like', '%线下%')->limit(5)->pluck('title') as $t) {
    echo $t . PHP_EOL;
}

echo PHP_EOL;
foreach (Article::query()->where('category_id', 69)->where('title', 'like', '%在线%')->where('title', 'not like', '%英文%')->limit(8)->pluck('title') as $t) {
    echo $t . PHP_EOL;
}
