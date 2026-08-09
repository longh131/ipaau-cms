<?php

require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$doc = [
    'type' => 'doc',
    'content' => [[
        'type' => 'paragraph',
        'content' => [[
            'type' => 'image',
            'attrs' => [
                'src' => '/storage/rich-editor/test.jpg',
                'alt' => null,
                'float' => 'left',
                'id' => 'rich-editor/test.jpg',
            ],
        ]],
    ]],
];

echo App\Support\RichContent::toHtml($doc).PHP_EOL;
