<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CourseRegistrationController extends Controller
{
    public function redirect(Request $request, int $course): RedirectResponse
    {
        $record = Course::query()->find($course);

        if ($record === null) {
            return redirect()->route('home');
        }

        $record->loadMissing('category');
        $listingUrl = $record->category
            ? route('category.show', $record->category->slug)
            : url('/');

        if (! $request->session()->has('ipa_member_id')) {
            return redirect()->route('member.login', ['redirect' => $listingUrl]);
        }

        if (! $record->is_active || ! $record->isRegistrationOpen()) {
            return redirect()
                ->to($listingUrl)
                ->with('course_registration_error', '报名已结束');
        }

        $registrationUrl = $record->resolvedRegistrationUrl();

        if (blank($registrationUrl)) {
            return redirect()
                ->to($listingUrl)
                ->with('course_registration_error', '暂无报名链接');
        }

        return redirect()->away($registrationUrl);
    }
}
