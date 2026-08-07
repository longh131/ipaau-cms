<?php

require dirname(__DIR__) . '/vendor/autoload.php';
$app = require dirname(__DIR__) . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Article;
use App\Models\Category;
use App\Support\CategoryListTemplate\CategoryListTemplateRegistry;
use App\Support\RichContent;

$category = Category::query()->find(69);
echo 'list_template=' . ($category->list_template ?? '') . PHP_EOL;
echo 'resolved=' . CategoryListTemplateRegistry::resolve($category) . PHP_EOL;
echo 'view=' . CategoryListTemplateRegistry::viewFor($category) . PHP_EOL;

$mismatch = Article::query()->where('category_id', 69)->whereColumn('sort_order', '!=', 'id')->count();
echo 'sort_order != id: ' . $mismatch . PHP_EOL;

$legacyCount = 0;
$oldCount = 0;

foreach (Article::query()->where('category_id', 69)->limit(200)->get(['content']) as $article) {
    $html = RichContent::toHtml($article->content) ?? '';
    preg_match_all('/\bsrc=(["\'])([^"\']+)\1/i', $html, $matches);
    foreach ($matches[2] as $src) {
        if (str_starts_with($src, '/assets/img/ipa-news-legacy/')) {
            $legacyCount++;
        } elseif (str_contains($src, 'uploadfile') || str_contains($src, 'news_img')) {
            $oldCount++;
        }
    }
}

echo 'legacy src (sample 200 articles): ' . $legacyCount . PHP_EOL;
echo 'unrewritten old src: ' . $oldCount . PHP_EOL;

$sample = Article::query()->where('category_id', 69)->where('slug', 'like', 'events-cpd-%')->first();
if ($sample) {
    echo 'sample slug=' . $sample->slug . ' title=' . mb_substr($sample->title, 0, 30) . PHP_EOL;
}
