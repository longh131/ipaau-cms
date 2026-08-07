<?php

require dirname(__DIR__) . '/vendor/autoload.php';
$app = require dirname(__DIR__) . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Article;
use App\Models\Category;
use App\Support\ArticleExtraFields;

$c = Category::query()->find(69);
$schema = ArticleExtraFields::normalizeSchema($c->article_extra_field_schema);
echo 'Parsed options:' . PHP_EOL;
print_r($schema[0]['options'] ?? []);

$withExtra = Article::query()->where('category_id', 69)->whereNotNull('extra_fields')->count();
echo 'Articles with extra_fields: ' . $withExtra . PHP_EOL;

// Parse title patterns
$patterns = [];
foreach (Article::query()->where('category_id', 69)->limit(500)->pluck('title') as $title) {
    if (preg_match('/【([^】]+)】/', $title, $m)) {
        $patterns[$m[1]] = ($patterns[$m[1]] ?? 0) + 1;
    }
}
arsort($patterns);
echo PHP_EOL . 'Top bracket tags:' . PHP_EOL;
foreach (array_slice($patterns, 0, 30, true) as $tag => $count) {
    echo $count . 'x ' . $tag . PHP_EOL;
}
