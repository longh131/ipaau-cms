@php
    $memberName = trim((string) ($member->full_name ?: $member->display_name));
    $creditLabel = rtrim(rtrim(number_format((float) $totalCredits, 1, '.', ''), '0'), '.');
@endphp
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>澳大利亚公共会计师协会（IPA AU）持续专业教育学分证明</title>
    <meta name="robots" content="nofollow,noindex">
    <style>
        body {
            margin: 0;
            color: #222;
            font-family: Arial, "Microsoft YaHei", sans-serif;
        }

        a {
            text-decoration: none;
            color: #e06c08;
        }

        a:hover {
            text-decoration: underline;
        }

        p {
            line-height: 160%;
        }

        .certificate {
            width: 800px;
            border: 1px solid #aaaaaa;
            padding: 15px;
            margin: 10px auto;
            box-sizing: border-box;
        }

        .certificate__banner {
            display: block;
            width: 100%;
            height: auto;
        }

        .certificate__title {
            font-size: 30px;
            font-weight: bold;
            text-align: center;
            margin-top: 40px;
            font-family: Arial, sans-serif;
        }

        .certificate__line {
            text-align: center;
            font-family: Arial, sans-serif;
            color: #808080;
        }

        .certificate__presented {
            font-size: 25px;
            margin-top: 40px;
        }

        .certificate__name {
            font-size: 25px;
            margin-top: 40px;
        }

        .certificate__member {
            font-size: 25px;
            margin-top: 20px;
        }

        .certificate__section {
            font-size: 20px;
            margin-top: 40px;
        }

        .certificate__table-wrap {
            margin-top: 20px;
            font-family: Arial, sans-serif;
            color: #808080;
        }

        .certificate__table {
            width: 100%;
            border-collapse: collapse;
        }

        .certificate__table td {
            border: 1px solid #000;
            padding: 10px;
            text-align: center;
            vertical-align: middle;
        }

        .certificate__issued {
            margin-top: 50px;
        }

        .certificate__sign {
            display: block;
            width: 200px;
            height: auto;
        }

        .noprint {
            margin: 0 auto;
            padding: 0;
            text-align: center;
        }

        .noprint input {
            background-color: #172967;
            color: #ffffff;
            border: 1px solid #172967;
            padding: 6px 16px;
            cursor: pointer;
        }

        @media print {
            .noprint {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="certificate">
        <div>
            <img class="certificate__banner" src="{{ asset('assets/img/cpd_score_banner.jpg') }}" alt="Continuing Professional Development">
        </div>

        <div class="certificate__title">Credit Certificate</div>
        <div class="certificate__line certificate__presented">Presented to</div>
        <div class="certificate__line certificate__name">{{ $memberName }}</div>
        <div class="certificate__line certificate__member">Member ID {{ $member->member_number }}</div>
        <div class="certificate__line certificate__section">For Attendance of</div>

        <div class="certificate__table-wrap">
            <table class="certificate__table">
                <tr>
                    <td width="33.3%"><b>Period</b></td>
                    <td width="33.3%"><b>Total Session Hours</b></td>
                    <td width="33.3%"><b>Total Credits</b></td>
                </tr>
                <tr>
                    <td>{{ $from->format('Y-m-d') }} 至 {{ $to->format('Y-m-d') }}</td>
                    <td>{{ $sessionCount }}</td>
                    <td>{{ $creditLabel }}</td>
                </tr>
            </table>
        </div>

        <div class="certificate__line certificate__section">Learning Record</div>
        <div class="certificate__table-wrap">
            <table class="certificate__table">
                <tr>
                    <td width="55%"><b>Event Title</b></td>
                    <td width="30%"><b>Date</b></td>
                    <td width="15%"><b>Credit</b></td>
                </tr>
                @forelse($records as $item)
                    <tr>
                        <td>{{ $item['title'] }}</td>
                        <td>{{ $item['starts_at']?->format('Y/n/j') ?: '—' }}</td>
                        <td>{{ $item['cpd_credits'] ?: '—' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3">所选期间内暂无到场 CPD 活动记录。</td>
                    </tr>
                @endforelse
            </table>
        </div>

        <div class="certificate__issued">Issued by: China Office</div>
        <div>Issue date:{{ now()->format('Y-m-d') }}</div>
        <div>Signed by:</div>
        <img class="certificate__sign" src="{{ asset('assets/img/sign.png') }}" alt="Rong CHEN">
        <div>Rong CHEN</div>
        <div>Group Director – China</div>
        <div>Institute of Public Accountants Group</div>
    </div>

    <p class="noprint">
        <input type="button" value="打印该页" onclick="window.print();"><br>
        为获得更好的打印效果，建议您使用IE7.0以上浏览器<br>
        在IE中的“工具”–“Internet选项”–“高级” - “打印背景颜色和图像”选项打勾。
    </p>
</body>
</html>
