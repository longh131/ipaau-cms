<?php

namespace App\Support;

use App\Models\Category;
use App\Models\IpaMember;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class MemberCategoryAccess
{
    public static function guard(Request $request, ?Category $category): ?RedirectResponse
    {
        if ($category === null || ! $category->requires_member_login) {
            return null;
        }

        if (! $request->session()->has('ipa_member_id')) {
            if ($request->isMethod('GET')) {
                $request->session()->put('url.intended', $request->fullUrl());
            }

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

        return null;
    }
}
