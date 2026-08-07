<?php

require dirname(__DIR__) . '/vendor/autoload.php';
$app = require dirname(__DIR__) . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

$row = DB::table('categories')->where('id', 69)->first(['article_extra_field_schema']);
$json = $row->article_extra_field_schema;
echo $json . PHP_EOL . PHP_EOL;

$decoded = json_decode($json, true);
$options = $decoded[0]['options'] ?? '';
echo 'Options string length: ' . strlen($options) . PHP_EOL;
echo 'Options repr: ';
for ($i = 0; $i < strlen($options); $i++) {
    echo sprintf('%02X ', ord($options[$i]));
}
echo PHP_EOL;
