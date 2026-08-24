<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CourseRegistrationController extends Controller
{
    public function redirect(Request $request, Course $course): RedirectResponse
    {
        if (! $request->session()->has('ipa_member_id')) {
            $request->session()->put('url.intended', route('courses.register', $course));

            return redirect()->route('member.login');
        }

        if (! $course->is_active || ! $course->isRegistrationOpen()) {
            $course->loadMissing('category');

            if ($course->category) {
                return redirect()
                    ->route('category.show', $course->category->slug)
                    ->with('course_registration_error', '报名已结束');
            }

            return redirect()->back()->with('course_registration_error', '报名已结束');
        }

        if (blank($course->resolvedRegistrationUrl())) {
            abort(404);
        }

        return redirect()->away((string) $course->resolvedRegistrationUrl());
    }
}
