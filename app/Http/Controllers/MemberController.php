<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Helpers\NavigationHelper;

class MemberController extends Controller
{
    public function show($id)
    {
        $member = Member::findOrFail($id);

        $pageData = [
            'member' => $member,
            'navigation' => NavigationHelper::getMainNavigation(),
            'footer' => []
        ];

        return view('frontend.member', compact('pageData'));
    }
}