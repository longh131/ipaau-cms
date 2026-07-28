<div class="cms-course-table-wrap">
    <table class="cms-course-table">
        <thead>
            <tr>
                <th scope="col">举办城市</th>
                <th scope="col">活动</th>
                <th scope="col">活动时间</th>
                <th scope="col">截止报名日期</th>
                <th scope="col">获得学分</th>
                <th scope="col">报名状态</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($courses as $course)
                <tr>
                    <td data-label="举办城市">{{ $course->city ?: '—' }}</td>
                    <td class="cms-course-table__activity" data-label="活动">
                        @if(filled($course->article_url))
                            <a href="{{ $course->article_url }}" target="_blank" rel="noopener noreferrer">
                                {{ $course->title }}
                            </a>
                        @else
                            {{ $course->title }}
                        @endif
                    </td>
                    <td data-label="活动时间">
                        {{ $course->starts_at?->format('Y-m-d') ?? '—' }}
                    </td>
                    <td data-label="截止报名日期">
                        {{ $course->registration_deadline?->format('Y-m-d') ?? '—' }}
                    </td>
                    <td data-label="获得学分">
                        {{ $course->formattedCpdCredits() ?? '—' }}
                    </td>
                    <td data-label="报名状态">
                        @if($course->isRegistrationOpen())
                            @if($course->canRegisterOnline())
                                <a
                                    href="{{ route('courses.register', $course) }}"
                                    class="cms-course-table__register-link"
                                >
                                    {{ $course->registrationStatusLabel() }}
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
