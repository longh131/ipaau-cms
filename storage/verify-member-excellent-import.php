<?php

require __DIR__.'/../vendor/autoload.php';

$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Article;
use App\Models\Category;
use App\Support\RichContent;

$category = Category::query()->find(79);

if ($category) {
    echo 'category: '.$category->name.' slug='.$category->slug.' template='.$category->list_template.PHP_EOL;
}

$article = Article::query()->where('slug', 'excellent-113')->first();

if ($article) {
    echo 'title: '.$article->title.PHP_EOL;
    echo 'summary len: '.strlen((string) $article->summary).PHP_EOL;
    echo 'position: '.str_replace("\n", ' | ', (string) ($article->extra_fields['position'] ?? '')).PHP_EOL;
    echo 'cover: '.($article->cover_image ?? 'null').PHP_EOL;
    $html = RichContent::toHtml($article->content) ?? '';
    echo 'content has legacy img: '.(str_contains($html, 'ipa-news-legacy') ? 'yes' : 'no').PHP_EOL;
}

$filenames = [];

foreach (Article::query()->where('category_id', 79)->get(['content', 'cover_image']) as $row) {
    if (filled($row->cover_image)) {
        $filenames[] = basename($row->cover_image);
    }

    $html = RichContent::toHtml($row->content) ?? '';

    if (preg_match_all('#/assets/img/ipa-news-legacy/([^"\'\s>]+)#', $html, $matches)) {
        foreach ($matches[1] as $filename) {
            $filenames[] = rawurldecode($filename);
        }
    }
}

$filenames = array_values(array_unique($filenames));
sort($filenames);

echo 'unique images: '.count($filenames).PHP_EOL;
