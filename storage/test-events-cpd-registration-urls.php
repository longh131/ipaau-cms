<?php

require dirname(__DIR__) . '/vendor/autoload.php';
$app = require dirname(__DIR__) . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Article;
use App\Support\CategoryListTemplate\EventsCpdTemplate;

$matched = 0;
$unmatched = 0;
$bySlug = [];

foreach (Article::query()->where('category_id', 69)->pluck('title', 'id') as $id => $title) {
    $url = EventsCpdTemplate::registrationUrlFromTitle($title);
    if ($url) {
        $matched++;
        $slug = basename(parse_url($url, PHP_URL_PATH) ?: '');
        $bySlug[$slug] = ($bySlug[$slug] ?? 0) + 1;
    } else {
        $unmatched++;
    }
}

echo "Matched: $matched, Unmatched: $unmatched" . PHP_EOL;
print_r($bySlug);
