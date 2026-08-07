<?php

require dirname(__DIR__) . '/vendor/autoload.php';
$app = require dirname(__DIR__) . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Article;
use App\Models\Category;

$c = Category::query()->find(69);
echo 'Schema: ' . json_encode($c?->article_extra_field_schema, JSON_UNESCAPED_UNICODE) . PHP_EOL;

$articles = Article::query()->where('category_id', 69)->whereNotNull('extra_fields')->limit(5)->get(['id', 'title', 'extra_fields']);
foreach ($articles as $a) {
    echo PHP_EOL . 'ID ' . $a->id . ' title: ' . mb_substr($a->title, 0, 40) . PHP_EOL;
    echo 'extra_fields: ' . json_encode($a->extra_fields, JSON_UNESCAPED_UNICODE) . PHP_EOL;
}

$bad = Article::query()->where('category_id', 69)->whereRaw("JSON_EXTRACT(extra_fields, '$') LIKE ?", ['%�%'])->limit(3)->get(['title', 'extra_fields']);
echo PHP_EOL . 'Bad encoding count sample: ' . $bad->count() . PHP_EOL;
foreach ($bad as $a) {
    echo json_encode($a->extra_fields, JSON_UNESCAPED_UNICODE) . ' | ' . mb_substr($a->title, 0, 50) . PHP_EOL;
}

// articles with 公开课 in title
$open = Article::query()->where('category_id', 69)->where('title', 'like', '%公开课%')->first(['title', 'extra_fields']);
if ($open) {
    echo PHP_EOL . 'Open course sample title: ' . $open->title . PHP_EOL;
    echo 'extra: ' . json_encode($open->extra_fields, JSON_UNESCAPED_UNICODE) . PHP_EOL;
}
