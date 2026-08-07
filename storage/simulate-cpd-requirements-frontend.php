<?php

require dirname(__DIR__) . '/vendor/autoload.php';
$app = require dirname(__DIR__) . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Page;
use App\Support\PageTemplate\Templates\GeneralSecondaryPageData;

$page = Page::query()->where('slug', 'cpd-requirements')->first();
$view = GeneralSecondaryPageData::forFrontend($page->data, $page);

echo 'has_content: ' . ($view['has_content'] ? 'yes' : 'no') . PHP_EOL;
echo 'sections count: ' . count($view['sections']) . PHP_EOL;

foreach ($view['sections'] as $i => $section) {
    echo PHP_EOL . "Section $i type=" . ($section['type'] ?? '') . PHP_EOL;
    if (($section['type'] ?? '') === 'content_block') {
        echo '  title: ' . ($section['title'] ?? '') . PHP_EOL;
        echo '  tagline: ' . ($section['tagline'] ?? '') . PHP_EOL;
        echo '  content_html: ' . ($section['content_html'] ?? '') . PHP_EOL;
    }
    if (($section['type'] ?? '') === 'html_body') {
        echo '  body length: ' . strlen($section['body_html'] ?? '') . PHP_EOL;
    }
}
