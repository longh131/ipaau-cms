<?php

namespace App\Services;

use App\Models\IpaMember;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MemberSmsService
{
    public function isValidMobile(string $mobile): bool
    {
        return (bool) preg_match(
            '/^(13[0-9]|14[01456879]|15[0-35-9]|16[2567]|17[0-8]|18[0-9]|19[0-35-9])\d{8}$/',
            $mobile,
        );
    }

    public function findLoginMember(string $mobile): ?IpaMember
    {
        $member = IpaMember::query()
            ->where('mobile_phone', $mobile)
            ->first();

        if ($member === null || ! $member->canLoginViaSms()) {
            return null;
        }

        return $member;
    }

    public function sendVerificationCode(string $mobile): array
    {
        if (! $this->isValidMobile($mobile)) {
            return ['ok' => false, 'message' => '手机号格式不合法！'];
        }

        if ($this->findLoginMember($mobile) === null) {
            return ['ok' => false, 'message' => '手机号码不存在！'];
        }

        $cooldownKey = "member_sms_cooldown:{$mobile}";
        $sentAt = Cache::get($cooldownKey);

        if ($sentAt !== null) {
            $remaining = 300 - (now()->timestamp - (int) $sentAt);

            if ($remaining > 0) {
                return ['ok' => false, 'message' => "请等待{$remaining}秒后再发送"];
            }
        }

        $code = $this->generateCode();
        $sent = $this->dispatchSms($mobile, $code);

        if (! $sent) {
            return ['ok' => false, 'message' => '发送失败，请联系客服人员！'];
        }

        Cache::put("member_sms_code:{$mobile}", $code, now()->addMinutes(5));
        Cache::put($cooldownKey, now()->timestamp, now()->addMinutes(5));

        return ['ok' => true, 'message' => '发送成功，请查看手机短信内容！'];
    }

    public function verifyCode(string $mobile, string $code): bool
    {
        $cached = Cache::get("member_sms_code:{$mobile}");

        return is_string($cached) && hash_equals($cached, trim($code));
    }

    private function generateCode(): string
    {
        return (string) random_int(100000, 999999);
    }

    private function dispatchSms(string $mobile, string $code): bool
    {
        if (config('services.member_sms.fake', false)) {
            Log::info('Member SMS (fake mode)', ['mobile' => $mobile, 'code' => $code]);

            return true;
        }

        $content = "验证码：{$code}，此验证码5分钟内有效，请及时验证，如非本人操作，请忽略此短信！【阿彼艾北京】";

        $response = Http::asForm()
            ->timeout(15)
            ->post(config('services.member_sms.endpoint'), [
                'action' => 'send',
                'userid' => config('services.member_sms.userid'),
                'account' => config('services.member_sms.account'),
                'password' => config('services.member_sms.password'),
                'content' => $content,
                'mobile' => $mobile,
            ]);

        if (! $response->successful()) {
            Log::warning('Member SMS HTTP error', ['status' => $response->status()]);

            return false;
        }

        return str_contains(strtolower($response->body()), 'success');
    }
}
