<?php

declare(strict_types=1);

$baseUrl = 'https://www.publicaccountants.org.au';
$outputDir = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'img' . DIRECTORY_SEPARATOR . 'board-exec';

$images = [
    'b1.jpg' => '/media/cmxbml5n/ipa_cheryl-mallett001.jpg',
    'b2.jpg' => '/media/1qtpzrol/ipa_julie-williams007_whitebg.jpg',
    'b3.jpg' => '/media/tiojpbjk/ipa-annette-tasker107.jpg',
    'b4.jpg' => '/media/33cbpn1j/ipa_richard-allen007.jpg',
];

if (! is_dir($outputDir)) {
    mkdir($outputDir, 0755, true);
}

$context = stream_context_create([
    'http' => [
        'header' => "User-Agent: Mozilla/5.0\r\n",
        'timeout' => 120,
    ],
]);

foreach ($images as $filename => $path) {
    $url = $baseUrl . $path;
    $target = $outputDir . DIRECTORY_SEPARATOR . $filename;
    echo "Downloading {$filename}...\n";

    $bytes = file_get_contents($url, false, $context);

    if ($bytes === false) {
        throw new RuntimeException("Failed to download {$url}");
    }

    if (function_exists('imagecreatefromstring')) {
        $image = @imagecreatefromstring($bytes);

        if ($image !== false) {
            imagejpeg($image, $target, 90);
            imagedestroy($image);
            echo "  Saved {$target}\n";
            continue;
        }
    }

    file_put_contents($target, $bytes);
    echo "  Saved raw bytes to {$target}\n";
}

echo "Done.\n";
