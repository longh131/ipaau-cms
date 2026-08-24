<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CPD 学分记录 · {{ $member->member_number }}</title>
    <link rel="stylesheet" href="{{ asset('assets/css/cpd-records.css') }}" />
    <style>
        body {
            font-family: Arial, "Microsoft YaHei", sans-serif;
            color: #222;
            margin: 24px;
        }

        .print-actions {
            margin-bottom: 24px;
        }

        @media print {
            .print-actions {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="print-actions">
        <button type="button" onclick="window.print()">打印</button>
    </div>

    <header class="cms-cpd-records-print__header">
        <h1>CPD 学分记录</h1>
        <p>会员号：{{ $member->member_number }} · {{ $member->display_name }}</p>
        <p>查询期间：{{ $from->format('Y/m/d') }} — {{ $to->format('Y/m/d') }}</p>
        <p>到场场次：{{ $sessionCount }} · 合计学分：{{ rtrim(rtrim(number_format($totalCredits, 1, '.', ''), '0'), '.') }}</p>
    </header>

    @if($records->isEmpty())
        <p>所选期间内暂无到场 CPD 活动记录。</p>
    @else
        <table class="cms-cpd-records-print__table">
            <thead>
                <tr>
                    <th>序号</th>
                    <th>活动</th>
                    <th>举办城市</th>
                    <th>活动时间</th>
                    <th>获得学分</th>
                    <th>出席状态</th>
                </tr>
            </thead>
            <tbody>
                @foreach($records as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $item['title'] }}</td>
                        <td>{{ $item['city'] ?: '—' }}</td>
                        <td>{{ $item['starts_at']?->format('Y-m-d') ?: '—' }}</td>
                        <td>{{ $item['cpd_credits'] ?: '—' }}</td>
                        <td>{{ $item['attend_label'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</body>
</html>
