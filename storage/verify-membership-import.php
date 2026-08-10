<?php

require __DIR__.'/../vendor/autoload.php';

$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Article;
use App\Support\LegacyNewsImageUrlRewriter;
use App\Support\RichContent;

$article = Article::query()->where('slug', 'interview-110')->first();

if ($article === null) {
    echo "article not found\n";
    exit(1);
}

$html = RichContent::toHtml($article->content);
echo 'title: '.$article->title.PHP_EOL;
echo 'cover: '.($article->cover_image ?? 'null').PHP_EOL;
echo substr($html, 0, 500).PHP_EOL;

$test = '<img src="/work_online/editor/../uploadfile/2026042761174089.jpg">';
$result = LegacyNewsImageUrlRewriter::rewriteContent($test);
echo 'rewrite test: '.$result['content'].PHP_EOL;

$withImages = Article::query()
    ->whereIn('category_id', [80, 81])
    ->get()
    ->filter(fn ($a) => str_contains(RichContent::toHtml($a->content) ?? '', 'ipa-news-legacy'));

echo 'articles with legacy images: '.$withImages->count().PHP_EOL;

$withUpload = Article::query()
    ->whereIn('category_id', [80, 81])
    ->get()
    ->filter(fn ($a) => str_contains(RichContent::toHtml($a->content) ?? '', 'uploadfile'));

echo 'articles still with uploadfile: '.$withUpload->count().PHP_EOL;
