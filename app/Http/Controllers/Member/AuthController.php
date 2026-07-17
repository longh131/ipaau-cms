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
    public function showLogin(): View|RedirectResponse
    {
        if (session()->has('ipa_member_id')) {
            return redirect()->route('member.dashboard');
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
            return back()->withErrors(['code' => '请输入手机号和验证码。']);
        }

        if (! $smsService->verifyCode($mobile, $code)) {
            return back()->withErrors(['code' => '验证码错误或已过期。'])->withInput();
        }

        $member = $smsService->findLoginMember($mobile);

        if ($member === null) {
            return back()->withErrors(['mobile' => '手机号码不存在！'])->withInput();
        }

        $request->session()->regenerate();
        $request->session()->put('ipa_member_id', $member->id);

        return redirect()->intended(route('member.dashboard'));
    }

    public function logout(Request $request): RedirectResponse
    {
        $request->session()->forget('ipa_member_id');
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('member.login');
    }
}
