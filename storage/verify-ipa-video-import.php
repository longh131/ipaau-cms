<?php

require dirname(__DIR__) . '/vendor/autoload.php';
$app = require dirname(__DIR__) . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Article;
use App\Models\Category;
use App\Support\RichContent;
use App\Support\VideoArticleContent;

foreach ([113, 114] as $id) {
    $c = Category::query()->find($id);
    echo $c->name . ' template=' . $c->list_template . ' count=' . Article::query()->where('category_id', $id)->count() . PHP_EOL;
}

$sample = Article::query()->where('category_id', 113)->where('title', 'Annette IPA 2021-中文')->first();
if ($sample) {
    echo PHP_EOL . 'Sample: ' . $sample->title . PHP_EOL;
    echo 'cover: ' . $sample->cover_image . PHP_EOL;
    echo 'html: ' . RichContent::toHtml($sample->content) . PHP_EOL;
}

$bakSample = Article::query()->where('category_id', 113)->where('title', '2026 CDIE回顾')->first();
if ($bakSample) {
    echo PHP_EOL . 'Bak sample: ' . $bakSample->title . PHP_EOL;
    echo 'cover: ' . $bakSample->cover_image . PHP_EOL;
    echo 'video: ' . VideoArticleContent::extractVideoUrl(RichContent::toHtml($bakSample->content)) . PHP_EOL;
}
