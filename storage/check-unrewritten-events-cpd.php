<?php

require dirname(__DIR__) . '/vendor/autoload.php';
$app = require dirname(__DIR__) . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Article;
use App\Support\RichContent;

$urls = [];

foreach (Article::query()->where('category_id', 69)->get(['id', 'slug', 'content']) as $article) {
    $html = RichContent::toHtml($article->content) ?? '';
    preg_match_all('/\bsrc=(["\'])([^"\']+)\1/i', $html, $matches);
    foreach ($matches[2] as $src) {
        if (! str_starts_with($src, '/assets/img/ipa-news-legacy/')) {
            $urls[$src] = ($urls[$src] ?? 0) + 1;
        }
    }
}

arsort($urls);
echo 'Unrewritten unique URLs: ' . count($urls) . PHP_EOL;
foreach (array_slice($urls, 0, 20, true) as $url => $count) {
    echo $count . 'x ' . $url . PHP_EOL;
}
