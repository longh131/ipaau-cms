<?php

require dirname(__DIR__) . '/vendor/autoload.php';
$app = require dirname(__DIR__) . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Article;
use App\Models\Category;

foreach ([97, 98, 111] as $id) {
    $c = Category::query()->find($id);
    $count = Article::query()->where('category_id', $id)->count();
    echo "Category $id: " . ($c->name ?? 'N/A') . " slug={$c->slug} articles=$count" . PHP_EOL;
}

$articles = Article::query()
    ->whereIn('category_id', [97, 98])
    ->orderByRaw('published_at IS NULL')
    ->orderBy('published_at')
    ->orderBy('id')
    ->get(['id', 'category_id', 'title', 'slug', 'published_at', 'sort_order']);

echo PHP_EOL . 'Total source articles: ' . $articles->count() . PHP_EOL;
foreach ($articles->take(3) as $a) {
    echo '  first: id='.$a->id.' cat='.$a->category_id.' pub='.$a->published_at.' '.$a->title.PHP_EOL;
}
foreach ($articles->slice(-3) as $a) {
    echo '  last: id='.$a->id.' cat='.$a->category_id.' pub='.$a->published_at.' '.$a->title.PHP_EOL;
}

$existing = Article::query()->where('category_id', 111)->count();
echo PHP_EOL . 'Target category 111 existing: ' . $existing . PHP_EOL;
