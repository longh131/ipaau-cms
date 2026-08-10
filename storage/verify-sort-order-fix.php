<?php

require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Article;
use App\Support\CategoryListTemplate\CategoryListTemplateRegistry;

foreach ([
    11 => '集团领导团队',
    12 => '集团会员连接团队',
    22 => '中国区团队',
    79 => '会员风采',
    80 => '会员专访',
    81 => '会员分享',
] as $catId => $label) {
    echo "=== {$label} ({$catId}) ===".PHP_EOL;
    $query = Article::query()->where('category_id', $catId)->where('is_active', true);
    CategoryListTemplateRegistry::applyArticleOrdering($query, \App\Models\Category::find($catId));
    $top = (clone $query)->limit(2)->get(['id', 'title', 'sort_order']);
    foreach ($top as $row) {
        echo "  TOP sort={$row->sort_order} id={$row->id} {$row->title}".PHP_EOL;
    }
    $bottom = Article::query()->where('category_id', $catId)->orderBy('sort_order')->limit(1)->first(['title', 'sort_order']);
    if ($bottom) {
        echo "  BOT sort={$bottom->sort_order} {$bottom->title}".PHP_EOL;
    }
}
