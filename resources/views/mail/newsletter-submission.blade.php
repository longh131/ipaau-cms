<x-mail::message>
# 新的评估意向

官网收到一条新的评估意向提交：

<x-mail::table>
| 字段 | 内容 |
|:-----|:-----|
| 姓名 | {{ $subscriber->name }} |
| 手机号 | {{ $subscriber->phone }} |
| 邮箱 | {{ $subscriber->email }} |
| 公司 | {{ $subscriber->company ?: '—' }} |
| 现任职务 | {{ $subscriber->job_title ?: '—' }} |
| 从哪里知晓IPA？ | {{ $subscriber->education ?: '—' }} |
| 提交时间 | {{ $subscriber->subscribed_at?->timezone(config('app.timezone'))->format('Y-m-d H:i:s') ?? '—' }} |
</x-mail::table>

请在后台「订阅管理」中查看完整记录。

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
