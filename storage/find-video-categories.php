<?php

require dirname(__DIR__) . '/vendor/autoload.php';
$app = require dirname(__DIR__) . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Category;

foreach (Category::query()->where('name', 'like', '%IPA%')->orWhere('name', 'like', '%播报%')->orWhere('name', 'like', '%活动回顾%')->get(['id','name','slug','list_template']) as $c) {
    echo $c->id . ' | ' . $c->name . ' | ' . $c->slug . ' | ' . ($c->list_template ?? '') . PHP_EOL;
}
