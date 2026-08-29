<?php

namespace App\Support\Bolue;

use App\Services\LearningPlatformSettingsService;

class BolueJumpUrlResolver
{
    public function __construct(
        private LearningPlatformSettingsService $learningPlatformSettings,
    ) {}

    public function resolve(?string $jumpUrl, ?string $target): string
    {
        $jumpUrl = trim((string) $jumpUrl);
        $target = trim((string) $target);

        if ($jumpUrl !== '' && $this->isAllowed($jumpUrl)) {
            return $jumpUrl;
        }

        if ($target === 'course-pack') {
            return $this->learningPlatformSettings->getPlatformADefaultJumpUrl();
        }

        $targets = config('bolue.targets', []);

        if ($target !== '' && isset($targets[$target]) && filled($targets[$target])) {
            return (string) $targets[$target];
        }

        return $this->learningPlatformSettings->getPlatformADefaultJumpUrl();
    }

    public function isAllowed(string $url): bool
    {
        $parts = parse_url($url);

        if (! is_array($parts)) {
            return false;
        }

        if (($parts['scheme'] ?? '') !== 'https') {
            return false;
        }

        $host = strtolower((string) ($parts['host'] ?? ''));

        if ($host === '' || ! in_array($host, config('bolue.allowed_hosts', []), true)) {
            return false;
        }

        $path = $parts['path'] ?? '';

        return is_string($path) && str_starts_with($path, '/');
    }
}
