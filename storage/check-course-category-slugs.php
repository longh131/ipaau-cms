<?php

require dirname(__DIR__) . '/vendor/autoload.php';
$app = require dirname(__DIR__) . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Category;

foreach (['china-online', 'china-offline', 'english-online', 'open-course'] as $slug) {
    $c = Category::query()->where('slug', $slug)->first(['id', 'name', 'slug']);
    echo ($c ? $c->id.' '.$c->name : 'MISSING').PHP_EOL;
}
