<x-filament-panels::page>
    <link rel="stylesheet" href="{{ asset('css/filament/member-statistics.css') }}" />

    @php
        $total = max(1, (int) ($this->stats['total'] ?? 0));
        $genders = $this->stats['gender'] ?? [];
        $male = $genders['男'] ?? 0;
        $female = $genders['女'] ?? 0;
        $statuses = $this->stats['statuses'] ?? [];
        $activeCount = collect($statuses)->filter(fn ($count, $label) => str_contains((string) $label, '活跃'))->sum();
    @endphp

    <div class="member-stats">
        {{-- 总览板块 --}}
        <section class="member-stats__section">
            <h2 class="member-stats__heading">总览</h2>
            <div class="member-stats__summary-grid">
                <article class="member-stats__summary-card member-stats__summary-card--primary">
                    <p class="member-stats__summary-label">持证会员总数</p>
                    <p class="member-stats__summary-value">{{ number_format($total) }}</p>
                </article>
                <article class="member-stats__summary-card">
                    <p class="member-stats__summary-label">男性会员</p>
                    <p class="member-stats__summary-value">{{ number_format($male) }}</p>
                    <p class="member-stats__summary-meta">{{ round($male / $total * 100, 1) }}%</p>
                </article>
                <article class="member-stats__summary-card">
                    <p class="member-stats__summary-label">女性会员</p>
                    <p class="member-stats__summary-value">{{ number_format($female) }}</p>
                    <p class="member-stats__summary-meta">{{ round($female / $total * 100, 1) }}%</p>
                </article>
                <article class="member-stats__summary-card">
                    <p class="member-stats__summary-label">活跃相关状态</p>
                    <p class="member-stats__summary-value">{{ number_format($activeCount) }}</p>
                    <p class="member-stats__summary-meta">含「活跃」字样</p>
                </article>
            </div>
        </section>

        {{-- 性别 + 年龄 --}}
        <div class="member-stats__two-col">
            <section class="member-stats__section">
                <h2 class="member-stats__heading">性别比例</h2>
                <div class="member-stats__card-grid member-stats__card-grid--compact">
                    @forelse($genders as $label => $count)
                        <article class="member-stats__metric-card">
                            <p class="member-stats__metric-label">{{ $label }}</p>
                            <p class="member-stats__metric-value">{{ number_format($count) }}</p>
                            <p class="member-stats__metric-meta">{{ round($count / $total * 100, 1) }}%</p>
                        </article>
                    @empty
                        <p class="member-stats__empty">暂无数据</p>
                    @endforelse
                </div>
            </section>

            <section class="member-stats__section">
                <h2 class="member-stats__heading">年龄结构</h2>
                <div class="member-stats__card-grid member-stats__card-grid--compact">
                    @forelse(($this->stats['age_groups'] ?? []) as $label => $count)
                        <article class="member-stats__metric-card">
                            <p class="member-stats__metric-label">{{ $label }}</p>
                            <p class="member-stats__metric-value">{{ number_format($count) }}</p>
                            <p class="member-stats__metric-meta">{{ round($count / $total * 100, 1) }}%</p>
                        </article>
                    @empty
                        <p class="member-stats__empty">暂无数据</p>
                    @endforelse
                </div>
            </section>
        </div>

        {{-- 会员类别 --}}
        <section class="member-stats__section">
            <h2 class="member-stats__heading">会员类别</h2>
            <div class="member-stats__card-grid">
                @forelse(array_slice($this->stats['levels'] ?? [], 0, 12, true) as $label => $count)
                    <article class="member-stats__metric-card member-stats__metric-card--accent">
                        <p class="member-stats__metric-label">{{ $label }}</p>
                        <p class="member-stats__metric-value">{{ number_format($count) }}</p>
                        <p class="member-stats__metric-meta">{{ round($count / $total * 100, 1) }}%</p>
                    </article>
                @empty
                    <p class="member-stats__empty">暂无数据</p>
                @endforelse
            </div>
        </section>

        {{-- 资格状态 --}}
        <section class="member-stats__section">
            <h2 class="member-stats__heading">会员资格状态</h2>
            <div class="member-stats__card-grid">
                @forelse($statuses as $label => $count)
                    <article class="member-stats__metric-card">
                        <p class="member-stats__metric-label">{{ $label }}</p>
                        <p class="member-stats__metric-value">{{ number_format($count) }}</p>
                        <p class="member-stats__metric-meta">{{ round($count / $total * 100, 1) }}%</p>
                    </article>
                @empty
                    <p class="member-stats__empty">暂无数据</p>
                @endforelse
            </div>
        </section>

        {{-- 入会年份 + 地区 --}}
        <div class="member-stats__two-col">
            <section class="member-stats__section">
                <h2 class="member-stats__heading">会籍时间（入会年份）</h2>
                <div class="member-stats__card-grid member-stats__card-grid--years">
                    @forelse($this->stats['join_years'] ?? [] as $year => $count)
                        <article class="member-stats__year-card">
                            <p class="member-stats__year">{{ $year }}</p>
                            <p class="member-stats__year-count">{{ number_format($count) }} 人</p>
                        </article>
                    @empty
                        <p class="member-stats__empty">暂无数据</p>
                    @endforelse
                </div>
            </section>

            <section class="member-stats__section">
                <h2 class="member-stats__heading">地区统计（Top 24）</h2>
                <div class="member-stats__card-grid member-stats__card-grid--regions">
                    @forelse(array_slice($this->stats['regions'] ?? [], 0, 24, true) as $region => $count)
                        <article class="member-stats__region-card">
                            <p class="member-stats__region-name">{{ $region }}</p>
                            <p class="member-stats__region-count">{{ number_format($count) }}</p>
                        </article>
                    @empty
                        <p class="member-stats__empty">暂无数据</p>
                    @endforelse
                </div>
            </section>
        </div>
    </div>
</x-filament-panels::page>
