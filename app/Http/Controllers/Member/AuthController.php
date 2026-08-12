<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\IpaMember;
use App\Services\MemberSmsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(Request $request): View|RedirectResponse
    {
        if (session()->has('ipa_member_id')) {
            if (! IpaMember::query()->whereKey(session('ipa_member_id'))->exists()) {
                session()->forget('ipa_member_id');
            } else {
                return redirect()->route('member.dashboard');
            }
        }

        if ($request->filled('redirect')
            && $this->isSafeRedirect($request->query('redirect'))
            && ! $this->isLoginUrl($request->query('redirect'))) {
            session(['url.intended' => $request->query('redirect')]);
        }

        return view('member.auth.login');
    }

    public function sendCode(Request $request, MemberSmsService $smsService): JsonResponse
    {
        $mobile = trim((string) $request->input('mobile', ''));
        $result = $smsService->sendVerificationCode($mobile);

        return response()->json($result, $result['ok'] ? 200 : 422);
    }

    public function verify(Request $request, MemberSmsService $smsService): RedirectResponse|JsonResponse
    {
        $mobile = trim((string) $request->input('mobile', ''));
        $code = trim((string) $request->input('code', ''));

        if ($mobile === '' || $code === '') {
            return $this->verifyFailure($request, ['code' => '请输入手机号和验证码。']);
        }

        if (! $smsService->verifyCode($mobile, $code)) {
            return $this->verifyFailure($request, ['code' => '验证码错误或已过期。'], $mobile);
        }

        $member = $smsService->findLoginMember($mobile);

        if ($member === null) {
            return $this->verifyFailure($request, ['mobile' => '手机号码不存在！'], $mobile);
        }

        $request->session()->regenerate();
        $request->session()->put('ipa_member_id', $member->id);

        $intended = $request->session()->pull('url.intended');

        if (filled($intended)
            && $this->isSafeRedirect($intended)
            && ! $this->isLoginUrl($intended)) {
            return $this->verifySuccess($request, $intended);
        }

        return $this->verifySuccess($request, route('member.dashboard'));
    }

    /**
     * @param  array<string, string>  $errors
     */
    private function verifyFailure(Request $request, array $errors, ?string $mobile = null): RedirectResponse|JsonResponse
    {
        if ($request->expectsJson()) {
            return response()->json([
                'ok' => false,
                'message' => reset($errors) ?: '登录失败，请重试。',
                'errors' => $errors,
            ], 422);
        }

        $redirect = back()->withErrors($errors);

        if ($mobile !== null) {
            $redirect->withInput(['mobile' => $mobile]);
        }

        return $redirect;
    }

    private function verifySuccess(Request $request, string $redirectTo): RedirectResponse|JsonResponse
    {
        if ($request->expectsJson()) {
            return response()->json([
                'ok' => true,
                'redirect' => $redirectTo,
            ]);
        }

        return redirect()->to($redirectTo);
    }

    public function logout(Request $request): RedirectResponse
    {
        $request->session()->forget('ipa_member_id');
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('member.login');
    }

    private function isSafeRedirect(?string $url): bool
    {
        if (blank($url)) {
            return false;
        }

        return str_starts_with($url, url('/'));
    }

    private function isLoginUrl(string $url): bool
    {
        return str_starts_with($url, route('member.login'));
    }
}
