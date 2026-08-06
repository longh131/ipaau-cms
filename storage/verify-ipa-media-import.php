<?php

require dirname(__DIR__) . '/vendor/autoload.php';
$app = require dirname(__DIR__) . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Article;
use App\Support\RichContent;

$mismatch = Article::query()->where('category_id', 70)->whereColumn('sort_order', '!=', 'id')->count();
echo 'sort_order != id: ' . $mismatch . PHP_EOL;

$withCover = Article::query()->where('category_id', 70)->whereNotNull('cover_image')->count();
echo 'with cover: ' . $withCover . PHP_EOL;

$sample = Article::query()->where('category_id', 70)->where('slug', 'media-43')->first();
if ($sample) {
    echo 'sample id=' . $sample->id . ' sort_order=' . $sample->sort_order . PHP_EOL;
    echo 'cover_image=' . $sample->cover_image . PHP_EOL;
    $html = RichContent::toHtml($sample->content) ?? '';
    preg_match('/\bsrc=(["\'])([^"\']+)\1/i', $html, $m);
    echo 'first img src=' . ($m[2] ?? 'none') . PHP_EOL;
}

$legacyCount = 0;
$otherCount = 0;

foreach (Article::query()->where('category_id', 70)->get(['content']) as $article) {
    $html = RichContent::toHtml($article->content) ?? '';
    preg_match_all('/\bsrc=(["\'])([^"\']+)\1/i', $html, $matches);
    foreach ($matches[2] as $src) {
        if (str_starts_with($src, '/assets/img/ipa-news-legacy/')) {
            $legacyCount++;
        } elseif (str_contains($src, 'news_img') || str_contains($src, 'uploadfile')) {
            $otherCount++;
        }
    }
}

echo 'legacy image src count: ' . $legacyCount . PHP_EOL;
echo 'unrewritten old src count: ' . $otherCount . PHP_EOL;
