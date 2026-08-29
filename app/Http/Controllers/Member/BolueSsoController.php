<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\IpaMember;
use App\Services\BolueSsoService;
use App\Support\Bolue\BolueJumpUrlResolver;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BolueSsoController extends Controller
{
    public function __invoke(
        Request $request,
        BolueSsoService $bolueSsoService,
        BolueJumpUrlResolver $jumpUrlResolver,
    ): RedirectResponse|View {
        $member = IpaMember::query()->findOrFail(session('ipa_member_id'));

        if (! filled($member->mobile_phone)) {
            return view('member.bolue.missing-phone');
        }

        $jumpUrl = $jumpUrlResolver->resolve(
            $request->query('jumpUrl'),
            $request->query('target'),
        );

        $result = $bolueSsoService->requestSsoRedirect(
            trim((string) $member->mobile_phone),
            $jumpUrl,
        );

        return match ($result['status']) {
            'success' => redirect()->away($result['url']),
            'account_not_found' => view('member.bolue.account-not-found'),
            default => view('member.bolue.service-unavailable', [
                'message' => $result['message'] ?? '暂时无法连接学习平台，请联系客服。',
            ]),
        };
    }
}
