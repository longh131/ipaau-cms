<?php

$base = dirname(__DIR__) . '/bak/IPA视频';
foreach (['IPA播报', 'IPA活动回顾'] as $dir) {
    $path = $base . DIRECTORY_SEPARATOR . $dir;
    echo $dir . ': ' . (is_dir($path) ? 'exists' : 'missing') . PHP_EOL;
    if (! is_dir($path)) {
        continue;
    }
    foreach (glob($path . '/*.mp4') ?: [] as $file) {
        echo '  ' . basename($file) . PHP_EOL;
    }
}
