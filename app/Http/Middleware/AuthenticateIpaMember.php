<?php

namespace App\Http\Middleware;

use App\Models\IpaMember;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateIpaMember
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->session()->has('ipa_member_id')) {
            return redirect()->route('member.login');
        }

        $member = IpaMember::query()->find($request->session()->get('ipa_member_id'));

        if ($member === null || ! $member->canAccessMemberPortal()) {
            $message = $member?->memberPortalLoginDeniedMessage()
                ?? '您的会籍状态不允许登录，请直接联系我们客服团队。';

            $request->session()->forget('ipa_member_id');

            return redirect()
                ->route('member.login')
                ->withErrors(['membership' => $message]);
        }

        return $next($request);
    }
}
