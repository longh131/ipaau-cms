<?php

declare(strict_types=1);

$baseUrl = 'https://www.publicaccountants.org.au';
$listUrl = $baseUrl . '/about-ipa/about-the-ipa/member-engagement-team/';
$outputDir = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'bak';

if (! is_dir($outputDir)) {
    mkdir($outputDir, 0755, true);
}

function fetchBytes(string $url): string
{
    $context = stream_context_create([
        'http' => [
            'header' => "User-Agent: Mozilla/5.0\r\n",
            'timeout' => 120,
        ],
        'ssl' => [
            'verify_peer' => true,
            'verify_peer_name' => true,
        ],
    ]);

    $bytes = @file_get_contents($url, false, $context);

    if ($bytes === false) {
        throw new RuntimeException("Failed to fetch: {$url}");
    }

    return $bytes;
}

function extractPageData(string $html): array
{
    if (! preg_match('/var pageData = (\{.*?\}); \/\/ comes from/s', $html, $matches)) {
        throw new RuntimeException('pageData not found in HTML');
    }

    $data = json_decode($matches[1], true);

    if (! is_array($data)) {
        throw new RuntimeException('Failed to decode pageData JSON');
    }

    return $data;
}

function absoluteUrl(string $baseUrl, string $path): string
{
    if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
        return $path;
    }

    return rtrim($baseUrl, '/') . '/' . ltrim($path, '/');
}

function stripImageQuery(string $url): string
{
    $parts = parse_url($url);

    if ($parts === false) {
        return $url;
    }

    $scheme = $parts['scheme'] ?? 'https';
    $host = $parts['host'] ?? '';
    $path = $parts['path'] ?? '';

    return "{$scheme}://{$host}{$path}";
}

/** @return array<int, array{name: string, url: string}> */
function extractTeamMembers(array $pageData): array
{
    $members = [];

    foreach ($pageData['result']['components'] ?? [] as $component) {
        if (($component['componentName'] ?? '') !== 'featureBlock') {
            continue;
        }

        foreach ($component['componentData']['teamMembers'] ?? [] as $member) {
            $url = $member['link']['url'] ?? null;

            if (! is_string($url) || $url === '') {
                continue;
            }

            $url = preg_replace('/#.*$/', '', $url) ?: $url;

            $members[] = [
                'name' => (string) ($member['memberName'] ?? ''),
                'url' => $url,
            ];
        }
    }

    return $members;
}

function extractBioImagePath(array $pageData): ?string
{
    foreach ($pageData['result']['components'] ?? [] as $component) {
        if (($component['componentName'] ?? '') !== 'ctaSection') {
            continue;
        }

        $desktop = $component['componentData']['desktopImage']['src'] ?? null;

        if (is_string($desktop) && $desktop !== '') {
            return $desktop;
        }
    }

    return null;
}

function saveAsJpeg(string $bytes, string $target): void
{
    if (! function_exists('imagecreatefromstring')) {
        file_put_contents($target, $bytes);

        return;
    }

    $image = @imagecreatefromstring($bytes);

    if ($image === false) {
        file_put_contents($target, $bytes);

        return;
    }

    imagejpeg($image, $target, 90);
    imagedestroy($image);
}

echo "Fetching member engagement team list...\n";
$listHtml = fetchBytes($listUrl);
$listData = extractPageData($listHtml);
$members = extractTeamMembers($listData);

if ($members === []) {
    throw new RuntimeException('No team members found on member engagement team list page.');
}

echo 'Found '.count($members)." members.\n";

foreach ($members as $index => $member) {
    $number = $index + 1;
    $memberUrl = absoluteUrl($baseUrl, $member['url']);
    $filename = 'a'.$number.'.jpg';
    $target = $outputDir . DIRECTORY_SEPARATOR . $filename;

    echo "[{$number}] {$member['name']} -> {$filename}\n";
    echo "  {$memberUrl}\n";

    $bioHtml = fetchBytes($memberUrl);
    $bioData = extractPageData($bioHtml);
    $imagePath = extractBioImagePath($bioData);

    if ($imagePath === null) {
        echo "  Skipped: no bio image found.\n";
        continue;
    }

    $imageUrl = stripImageQuery(absoluteUrl($baseUrl, $imagePath));
    $imageBytes = fetchBytes($imageUrl);
    saveAsJpeg($imageBytes, $target);

    echo "  Saved {$filename} from {$imageUrl}\n";
}

echo "Done. Files saved to {$outputDir}\n";
