<?php

require dirname(__DIR__) . '/vendor/autoload.php';
$app = require dirname(__DIR__) . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Category;
use App\Models\SpecialCategoryPage;

$c = Category::query()->find(71);
echo json_encode($c?->only(['id','name','slug','type','list_template']), JSON_UNESCAPED_UNICODE) . PHP_EOL;
echo 'Special page: ' . json_encode(SpecialCategoryPage::query()->where('category_id', 71)->first()?->toArray(), JSON_UNESCAPED_UNICODE) . PHP_EOL;

foreach ([113, 114] as $id) {
    $cat = Category::query()->find($id);
    echo $id . ' ' . $cat?->slug . PHP_EOL;
}
