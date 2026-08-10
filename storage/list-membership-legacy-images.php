<?php

require __DIR__.'/../vendor/autoload.php';

$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Article;
use App\Support\RichContent;

$filenames = [];

foreach (Article::query()->whereIn('category_id', [80, 81])->get(['content', 'cover_image']) as $article) {
    if (filled($article->cover_image)) {
        $filenames[] = basename($article->cover_image);
    }

    $html = RichContent::toHtml($article->content) ?? '';

    if (preg_match_all('#/assets/img/ipa-news-legacy/([^"\'\s>]+)#', $html, $matches)) {
        foreach ($matches[1] as $filename) {
            $filenames[] = rawurldecode($filename);
        }
    }
}

$filenames = array_values(array_unique($filenames));
sort($filenames);

echo 'category 80 count: '.Article::query()->where('category_id', 80)->count().PHP_EOL;
echo 'category 81 count: '.Article::query()->where('category_id', 81)->count().PHP_EOL;
echo 'unique legacy images: '.count($filenames).PHP_EOL.PHP_EOL;

foreach ($filenames as $filename) {
    echo $filename.PHP_EOL;
}
