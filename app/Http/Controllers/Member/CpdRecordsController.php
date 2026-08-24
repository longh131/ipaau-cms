<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\IpaMember;
use App\Services\CpeMemberService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CpdRecordsController extends Controller
{
    public function search(Request $request, CpeMemberService $service): RedirectResponse
    {
        $member = $this->currentMember();
        $from = $this->parseDate($request->input('from'));
        $to = $this->parseDate($request->input('to'));

        if ($from === null || $to === null) {
            return redirect()
                ->route('category.show', 'my-cpd-records')
                ->withErrors(['cpd_search' => '请选择有效的开始和结束日期。']);
        }

        if ($from->gt($to)) {
            return redirect()
                ->route('category.show', 'my-cpd-records')
                ->withErrors(['cpd_search' => '开始日期不能晚于结束日期。']);
        }

        $result = $service->memberSessionsInRange($member->member_number, $from, $to);

        return redirect()
            ->route('category.show', 'my-cpd-records')
            ->with('cpd_search_from', $from->toDateString())
            ->with('cpd_search_to', $to->toDateString())
            ->with('cpd_search_result', [
                'session_count' => $result['session_count'],
                'total_credits' => $result['total_credits'],
            ]);
    }

    public function print(Request $request, CpeMemberService $service): View|RedirectResponse
    {
        $member = $this->currentMember();
        $from = $this->parseDate($request->query('from'));
        $to = $this->parseDate($request->query('to'));

        if ($from === null || $to === null) {
            return redirect()
                ->route('category.show', 'my-cpd-records')
                ->withErrors(['cpd_search' => '请先选择查询日期范围。']);
        }

        $result = $service->memberSessionsInRange($member->member_number, $from, $to);

        return view('member.cpd-records.print', [
            'member' => $member,
            'from' => $from,
            'to' => $to,
            'sessionCount' => $result['session_count'],
            'totalCredits' => $result['total_credits'],
            'records' => $result['records'],
        ]);
    }

    private function currentMember(): IpaMember
    {
        return IpaMember::query()->findOrFail(session('ipa_member_id'));
    }

    private function parseDate(mixed $value): ?Carbon
    {
        $value = trim((string) ($value ?? ''));

        if ($value === '') {
            return null;
        }

        try {
            return Carbon::parse($value)->startOfDay();
        } catch (\Throwable) {
            return null;
        }
    }
}
