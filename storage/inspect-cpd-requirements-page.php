<?php

require dirname(__DIR__) . '/vendor/autoload.php';
$app = require dirname(__DIR__) . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Category;
use App\Models\Page;

$category = Category::query()->where('slug', 'cpd-requirements')->first();
echo 'Category: ' . json_encode($category?->only(['id', 'name', 'type', 'introduction']), JSON_UNESCAPED_UNICODE) . PHP_EOL;

$page = Page::query()->where('category_id', $category?->id)->first();
echo 'Page: ' . json_encode($page?->only(['id', 'slug', 'template', 'title', 'is_active']), JSON_UNESCAPED_UNICODE) . PHP_EOL;

if ($page) {
    $data = $page->data;
    echo 'Data keys: ' . implode(', ', array_keys(is_array($data) ? $data : [])) . PHP_EOL;
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
}
