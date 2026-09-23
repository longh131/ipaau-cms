<div class="cms-course-table-wrap">
    <table class="cms-course-table">
        <colgroup>
            <col class="cms-course-table__col-city" />
            <col class="cms-course-table__col-activity" />
            <col class="cms-course-table__col-time" />
            <col class="cms-course-table__col-deadline" />
            <col class="cms-course-table__col-credits" />
            <col class="cms-course-table__col-status" />
        </colgroup>
        <thead>
            <tr>
                <th scope="col">形式/城市</th>
                <th scope="col">主题</th>
                <th scope="col">日期</th>
                <th scope="col">报名截止日</th>
                <th scope="col">学分</th>
                <th scope="col">报名</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($courses as $course)
                <tr>
                    <td data-label="形式/城市">{{ $course->city ?: '—' }}</td>
                    <td class="cms-course-table__activity" data-label="主题">
                        @if(filled($course->article_url))
                            <a href="{{ $course->article_url }}" target="_blank" rel="noopener noreferrer">
                                {{ $course->title }}
                            </a>
                        @else
                            {{ $course->title }}
                        @endif
                    </td>
                    <td class="cms-course-table__time" data-label="日期">
                        {{ $course->starts_at?->format('Y-m-d') ?? '—' }}
                    </td>
                    <td data-label="报名截止日">
                        {{ $course->registration_deadline?->format('Y-m-d') ?? '—' }}
                    </td>
                    <td class="cms-course-table__credits" data-label="学分">
                        {{ $course->formattedCpdCredits() ?? '—' }}
                    </td>
                    <td class="cms-course-table__status" data-label="报名">
                        @if($course->isRegistrationOpen())
                            @if(session()->has('ipa_member_id') && $course->canRegisterOnline())
                                <a
                                    href="{{ route('courses.register', $course) }}"
                                    class="cms-course-table__register-link"
                                >
                                    {{ $course->registrationStatusLabel() }}
                                </a>
                            @elseif(! session()->has('ipa_member_id'))
                                <a
                                    href="{{ route('member.login', ['redirect' => route('courses.register', $course)]) }}"
                                    class="cms-course-table__register-link"
                                >
                                    请登录报名
                                </a>
                            @else
                                <span class="cms-course-table__register-open">
                                    {{ $course->registrationStatusLabel() }}
                                </span>
                            @endif
                        @else
                            <span class="cms-course-table__register-closed">
                                {{ $course->registrationStatusLabel() }}
                            </span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
