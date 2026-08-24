<x-filament-panels::page>
    <link rel="stylesheet" href="{{ asset('css/filament/course-registrations.css') }}" />

    @php
        $groupStyles = [
            'registered' => [
                'summary' => 'course-registrations__summary-card--registered',
                'header' => 'course-registrations__section-header--registered',
                'count' => '',
            ],
            'present' => [
                'summary' => 'course-registrations__summary-card--present',
                'header' => 'course-registrations__section-header--present',
                'count' => 'course-registrations__section-count--present',
            ],
            'absent' => [
                'summary' => 'course-registrations__summary-card--absent',
                'header' => 'course-registrations__section-header--absent',
                'count' => 'course-registrations__section-count--absent',
            ],
        ];
    @endphp

    <div class="course-registrations">
        <section class="course-registrations__hero">
            <h2 class="course-registrations__hero-title">{{ $record->title }}</h2>

            <div class="course-registrations__meta">
                <span class="course-registrations__meta-item">
                    课程 ID：<strong>{{ $record->getKey() }}</strong>
                </span>
                @if($record->legacy_id)
                    <span class="course-registrations__meta-item">
                        旧 ipa_book ID：<strong>{{ $record->legacy_id }}</strong>
                    </span>
                @endif
                <span class="course-registrations__meta-item">
                    开课时间：<strong>{{ $record->starts_at?->format('Y-m-d') ?: '—' }}</strong>
                </span>
                <span class="course-registrations__meta-item">
                    举办城市：<strong>{{ $record->city ?: '—' }}</strong>
                </span>
            </div>
        </section>

        <div class="course-registrations__summary">
            @foreach($groups as $key => $group)
                @php($style = $groupStyles[$key] ?? $groupStyles['registered'])
                <article @class(['course-registrations__summary-card', $style['summary']])>
                    <p class="course-registrations__summary-label">{{ $group['title'] }}</p>
                    <p class="course-registrations__summary-value">{{ number_format(count($group['rows'])) }}</p>
                </article>
            @endforeach
        </div>

        @foreach($groups as $key => $group)
            @php($style = $groupStyles[$key] ?? $groupStyles['registered'])
            <section class="course-registrations__section">
                <header @class(['course-registrations__section-header', $style['header']])>
                    <h3 class="course-registrations__section-title">{{ $group['title'] }}</h3>
                    <span @class(['course-registrations__section-count', $style['count']])>
                        {{ number_format(count($group['rows'])) }}
                    </span>
                </header>

                @if($group['rows'] === [])
                    <p class="course-registrations__empty">暂无记录</p>
                @else
                    <div class="course-registrations__table-wrap">
                        <table class="course-registrations__table">
                            <thead>
                                <tr>
                                    <th scope="col">序号</th>
                                    <th scope="col">会员号</th>
                                    <th scope="col">中文名</th>
                                    <th scope="col">性别</th>
                                    <th scope="col">Email</th>
                                    <th scope="col">联系电话</th>
                                    <th scope="col">报名方式</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($group['rows'] as $row)
                                    <tr>
                                        <td class="course-registrations__index">{{ $row['index'] }}</td>
                                        <td class="course-registrations__member-no">{{ $row['member_number'] }}</td>
                                        <td class="course-registrations__name">{{ $row['full_name'] }}</td>
                                        <td>{{ $row['gender'] }}</td>
                                        <td>
                                            @if(filled($row['email']) && $row['email'] !== '—')
                                                <span class="course-registrations__email" title="{{ $row['email'] }}">
                                                    {{ $row['email'] }}
                                                </span>
                                            @else
                                                <span class="course-registrations__dash">—</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="course-registrations__phones">
                                                <div class="course-registrations__phone-line">
                                                    <span class="course-registrations__phone-label">手机</span>
                                                    <span>{{ $row['mobile_phone'] ?: '—' }}</span>
                                                </div>
                                                <div class="course-registrations__phone-line">
                                                    <span class="course-registrations__phone-label">工作</span>
                                                    <span>{{ $row['work_phone'] ?: '—' }}</span>
                                                </div>
                                                <div class="course-registrations__phone-line">
                                                    <span class="course-registrations__phone-label">家庭</span>
                                                    <span>{{ $row['home_phone'] ?: '—' }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            @if(filled($row['registration_method']) && $row['registration_method'] !== '—')
                                                <span class="course-registrations__method">{{ $row['registration_method'] }}</span>
                                            @else
                                                <span class="course-registrations__dash">—</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </section>
        @endforeach
    </div>
</x-filament-panels::page>
