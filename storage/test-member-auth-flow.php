<?php

require dirname(__DIR__).'/vendor/autoload.php';

$app = require dirname(__DIR__).'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

function request(string $method, string $uri, array $cookies = [], ?string $body = null, array $headers = []): array
{
    global $kernel;

    $server = [
        'REQUEST_METHOD' => $method,
        'REQUEST_URI' => $uri,
        'HTTP_HOST' => 'ipaau-cms.test',
        'SERVER_NAME' => 'ipaau-cms.test',
        'SERVER_PORT' => '80',
        'HTTPS' => 'off',
        'HTTP_ACCEPT' => 'text/html,application/json',
    ];

    foreach ($headers as $key => $value) {
        $server['HTTP_'.str_replace('-', '_', strtoupper($key))] = $value;
    }

    if ($cookies !== []) {
        $server['HTTP_COOKIE'] = implode('; ', array_map(
            fn ($k, $v) => $k.'='.$v,
            array_keys($cookies),
            array_values($cookies),
        ));
    }

    $request = Illuminate\Http\Request::create($uri, $method, [], $cookies, [], $server, $body);

    $response = $kernel->handle($request);
    $kernel->terminate($request, $response);

    $setCookies = [];
    foreach ($response->headers->getCookies() as $cookie) {
        $setCookies[$cookie->getName()] = $cookie->getValue();
    }

    return [
        'status' => $response->getStatusCode(),
        'body' => $response->getContent(),
        'cookies' => $setCookies,
    ];
}

$cookies = [];

$login = request('GET', '/member/login', $cookies);
$cookies = array_merge($cookies, $login['cookies']);

preg_match('/name="csrf-token" content="([^"]+)"/', $login['body'], $matches);
$token = $matches[1] ?? '';

echo "Login status: {$login['status']}\n";
echo "CSRF: {$token}\n";

$send = request('POST', '/member/send-code', $cookies, json_encode(['mobile' => '13800138000']), [
    'Content-Type' => 'application/json',
    'X-CSRF-TOKEN' => $token,
    'X-Requested-With' => 'XMLHttpRequest',
]);
$cookies = array_merge($cookies, $send['cookies']);

echo "Send-code status: {$send['status']}\n";
echo "Send-code body: {$send['body']}\n";

$verifyBody = http_build_query([
    '_token' => $token,
    'mobile' => '13800138000',
    'code' => '000000',
]);

$verify = request('POST', '/member/verify', $cookies, $verifyBody, [
    'Content-Type' => 'application/x-www-form-urlencoded',
]);
$cookies = array_merge($cookies, $verify['cookies']);

echo "Verify status: {$verify['status']}\n";
echo str_contains($verify['body'], '419') || str_contains($verify['body'], 'Page Expired')
    ? "Verify result: 419 Page Expired\n"
    : "Verify result: OK (not 419)\n";
