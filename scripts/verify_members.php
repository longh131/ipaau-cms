<?php

require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo 'Total: '.App\Models\IpaMember::count().PHP_EOL;

$member = App\Models\IpaMember::query()
    ->whereNotNull('mobile_phone')
    ->where('member_level', 'not like', '%非会员%')
    ->first();

if ($member) {
    echo "Sample: {$member->mobile_phone} | {$member->member_level} | {$member->display_name}".PHP_EOL;
}

$stats = app(App\Services\MemberStatisticsService::class)->summary();
echo 'Genders: '.json_encode($stats['gender'], JSON_UNESCAPED_UNICODE).PHP_EOL;
