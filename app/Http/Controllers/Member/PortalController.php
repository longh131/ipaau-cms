<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\IpaMember;
use App\Support\MemberFieldMap;
use Illuminate\View\View;

class PortalController extends Controller
{
    public function dashboard(): View
    {
        $member = $this->currentMember();

        return view('member.dashboard', [
            'member' => $member,
        ]);
    }

    public function profile(): View
    {
        $member = $this->currentMember();

        return view('member.profile', [
            'member' => $member,
            'profileFields' => MemberFieldMap::profileFields(),
            'overviewFields' => [
                'member_level',
                'joined_at',
                'region',
                'level_valid_until',
            ],
            'overviewLabels' => [
                'member_level' => '会员级别',
                'joined_at' => '加入日期',
                'region' => '持证会籍资格所属区',
                'level_valid_until' => '会员等级有效期',
            ],
            'membershipFields' => array_diff(
                array_keys(MemberFieldMap::DB_LABELS),
                MemberFieldMap::profileFields(),
            ),
            'fieldLabels' => MemberFieldMap::DB_LABELS,
        ]);
    }

    private function currentMember(): IpaMember
    {
        return IpaMember::query()->findOrFail(session('ipa_member_id'));
    }
}
