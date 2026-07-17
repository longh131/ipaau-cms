<?php

require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$member = App\Models\IpaMember::query()->updateOrCreate(
    ['member_number' => 'TEST-KENNY-001'],
    [
        'full_name' => 'Kenny',
        'first_name' => 'Kenny',
        'mobile_phone' => '13911015850',
        'member_level' => 'AIPA',
        'member_level_short' => 'AIPA',
        'membership_status' => '活跃会员',
        'email' => 'kenny@test.local',
        'region' => '中国',
        'joined_at' => now()->subYears(2)->format('Y-m-d'),
        'level_valid_until' => now()->addYear()->format('Y-m-d'),
    ],
);

echo "OK: {$member->member_number} | {$member->full_name} | {$member->mobile_phone}".PHP_EOL;
