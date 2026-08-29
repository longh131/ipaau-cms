<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class BolueSsoService
{
    public function __construct(
        private LearningPlatformSettingsService $learningPlatformSettings,
    ) {}

    /**
     * @return array{status: 'success', url: string}|array{status: 'account_not_found'}|array{status: 'service_unavailable', message: string}
     */
    public function requestSsoRedirect(string $phone, string $jumpUrl): array
    {
        $platformConfig = $this->learningPlatformSettings->getPlatformAConfig();
        $code = $platformConfig['api_code'];
        $secret = $platformConfig['api_secret'];
        $apiUrl = $platformConfig['api_url'];

        if ($code === '' || $secret === '') {
            Log::error('Bolue SSO credentials are not configured.');

            return [
                'status' => 'service_unavailable',
                'message' => '暂时无法连接学习平台，请联系客服。',
            ];
        }

        $ts = (string) (int) round(microtime(true) * 1000);
        $payload = $this->buildPayload($code, $secret, $phone, $ts);

        try {
            $responseBody = $this->postJson($apiUrl, $payload);
        } catch (RuntimeException $exception) {
            Log::warning('Bolue SSO API request failed.', [
                'message' => $exception->getMessage(),
            ]);

            return [
                'status' => 'service_unavailable',
                'message' => '系统繁忙，请稍后重试。',
            ];
        }

        $authCode = $this->extractAuthCode($responseBody);

        if ($authCode === null) {
            return ['status' => 'account_not_found'];
        }

        $ssoLoginUrl = $platformConfig['sso_login_url'];
        $query = http_build_query([
            'authCode' => $authCode,
            'jumpUrl' => $jumpUrl,
        ]);

        return [
            'status' => 'success',
            'url' => $ssoLoginUrl.'?'.$query,
        ];
    }

    /**
     * @return array{code: string, ts: string, sign: string, data: string}
     */
    private function buildPayload(string $code, string $secret, string $phone, string $ts): array
    {
        $oriDataStr = json_encode(['phone' => $phone], JSON_UNESCAPED_UNICODE);
        $keyStr = $secret.$ts;
        $data = $this->encryptData($oriDataStr, $keyStr);
        $oriStr = $code.substr($ts, 0, 10).$data;
        $signStr = md5($oriStr);
        $sign = substr($signStr, 0, -2);

        return [
            'code' => $code,
            'ts' => $ts,
            'sign' => $sign,
            'data' => $data,
        ];
    }

    private function encryptData(string $plainText, string $keyStr): string
    {
        $key = md5($keyStr, true);
        $encrypted = openssl_encrypt($plainText, 'AES-128-ECB', $key, OPENSSL_RAW_DATA);

        if ($encrypted === false) {
            throw new RuntimeException('Bolue payload encryption failed.');
        }

        return base64_encode($encrypted);
    }

    /**
     * @param  array{code: string, ts: string, sign: string, data: string}  $payload
     */
    private function postJson(string $url, array $payload): string
    {
        $timeout = (int) config('bolue.http_timeout', 30);
        $retries = max(1, (int) config('bolue.http_retries', 3));
        $delayMs = max(0, (int) config('bolue.http_retry_delay_ms', 1000));
        $lastException = null;

        for ($attempt = 1; $attempt <= $retries; $attempt++) {
            try {
                $response = Http::timeout($timeout)
                    ->acceptJson()
                    ->asJson()
                    ->post($url, $payload);

                if ($response->failed()) {
                    throw new RuntimeException('Bolue API returned HTTP '.$response->status());
                }

                return $response->body();
            } catch (RuntimeException $exception) {
                $lastException = $exception;

                if ($attempt < $retries && $delayMs > 0) {
                    usleep($delayMs * 1000);
                }
            } catch (\Throwable $exception) {
                $lastException = new RuntimeException($exception->getMessage(), 0, $exception);

                if ($attempt < $retries && $delayMs > 0) {
                    usleep($delayMs * 1000);
                }
            }
        }

        throw $lastException ?? new RuntimeException('Bolue API request failed.');
    }

    private function extractAuthCode(string $responseBody): ?string
    {
        $decoded = json_decode($responseBody, true);

        if (is_array($decoded)) {
            $authCode = data_get($decoded, 'result.authCode');

            if (is_string($authCode) && $authCode !== '') {
                return $authCode;
            }
        }

        if (! str_contains($responseBody, 'authCode')) {
            return null;
        }

        if (preg_match('/"authCode"\s*:\s*"([^"]+)"/', $responseBody, $matches) === 1) {
            return $matches[1] !== '' ? $matches[1] : null;
        }

        return null;
    }
}
