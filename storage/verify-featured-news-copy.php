<?php

require dirname(__DIR__) . '/vendor/autoload.php';
$app = require dirname(__DIR__) . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Article;

$total = Article::query()->where('category_id', 111)->count();
echo "Total in 111: $total" . PHP_EOL;

$from97 = Article::query()->where('category_id', 111)->where('slug', 'like', 'featured-news-%')->whereIn('slug', 
    Article::query()->whereIn('category_id', [97])->pluck('id')->map(fn ($id) => 'featured-news-'.$id)
)->count();

// count by source via slug id
$slugs97 = Article::query()->where('category_id', 97)->pluck('id')->map(fn ($id) => 'featured-news-'.$id)->all();
$slugs98 = Article::query()->where('category_id', 98)->pluck('id')->map(fn ($id) => 'featured-news-'.$id)->all();
$c97 = Article::query()->where('category_id', 111)->whereIn('slug', $slugs97)->count();
$c98 = Article::query()->where('category_id', 111)->whereIn('slug', $slugs98)->count();
echo "From 97: $c97, From 98: $c98" . PHP_EOL;

$ordered = Article::query()->where('category_id', 111)
    ->orderByRaw('published_at IS NULL')
    ->orderBy('published_at')
    ->orderBy('id')
    ->get(['id', 'slug', 'published_at', 'title']);

echo PHP_EOL . 'Earliest 3:' . PHP_EOL;
foreach ($ordered->take(3) as $a) {
    echo "  {$a->published_at} {$a->title}" . PHP_EOL;
}
echo 'Latest 3:' . PHP_EOL;
foreach ($ordered->slice(-3) as $a) {
    echo "  {$a->published_at} {$a->title}" . PHP_EOL;
}

$nullPub = Article::query()->where('category_id', 111)->whereNull('published_at')->count();
echo PHP_EOL . "Null published_at: $nullPub" . PHP_EOL;
