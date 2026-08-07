<?php

require dirname(__DIR__) . '/vendor/autoload.php';

use App\Support\ArticleExtraFields;
use Illuminate\Support\Facades\DB;

$app = require dirname(__DIR__) . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$row = DB::table('categories')->where('id', 69)->first(['article_extra_field_schema']);
$decoded = json_decode($row->article_extra_field_schema, true);
$rawOptions = $decoded[0]['options'];

echo 'Raw options:' . PHP_EOL . $rawOptions . PHP_EOL . PHP_EOL;

$parsed = ArticleExtraFields::parseSelectOptions($rawOptions);
var_export($parsed);
echo PHP_EOL . PHP_EOL;

$schema = ArticleExtraFields::normalizeSchema($decoded);
var_export($schema[0]['options'] ?? null);
echo PHP_EOL;

foreach ($parsed as $k => $v) {
    echo bin2hex($k) . ' => ' . bin2hex($v) . PHP_EOL;
}
