@php
    $lookupResult = is_array($lookupResult ?? null) ? $lookupResult : null;
    $lookupStatus = (string) ($lookupResult['status'] ?? '');
    $hasSubmitted = $lookupResult !== null;
@endphp

<div class="cms-certificate-lookup max-w-3xl mx-auto">
    <div class="cms-certificate-lookup__heading text-center">
        <h2 class="font-apex-book cms-section-title text-secondary mb-0">
            {{ $certificateTitle }}
        </h2>

        @if(filled($certificateSummary))
            <p class="cms-certificate-lookup__summary mt-6 mb-0 text-lg font-din text-primary">
                {{ $certificateSummary }}
            </p>
        @endif
    </div>

    <form
        method="POST"
        action="{{ route('category.certificate-lookup', $category->slug) }}"
        class="cms-certificate-lookup__form mt-10"
        novalidate
    >
        @csrf

        <div class="cms-certificate-lookup__field">
            <label for="certificate-full-name" class="cms-certificate-lookup__label">会员姓名</label>
            <input
                id="certificate-full-name"
                type="text"
                name="full_name"
                value="{{ old('full_name') }}"
                class="cms-certificate-lookup__input"
                autocomplete="name"
                required
            />
            @error('full_name')
                <p class="cms-certificate-lookup__error">{{ $message }}</p>
            @enderror
        </div>

        <div class="cms-certificate-lookup__field">
            <label for="certificate-member-number" class="cms-certificate-lookup__label">证书编号</label>
            <input
                id="certificate-member-number"
                type="text"
                name="member_number"
                value="{{ old('member_number') }}"
                class="cms-certificate-lookup__input"
                autocomplete="off"
                required
            />
            @error('member_number')
                <p class="cms-certificate-lookup__error">{{ $message }}</p>
            @enderror
        </div>

        <div class="cms-certificate-lookup__actions">
            <button type="submit" class="cms-certificate-lookup__submit cta group font-medium uppercase border-2 border-link bg-link text-white hover:bg-link-hover hover:border-link-hover flex transition-all duration-300 uppercase text-lg px-[24px] py-[11.5px] sm:px-[32px] sm:py-[15.5px] rounded-full">
                <span class="flex flex-nowrap items-center justify-center w-full uppercase">证书查询</span>
            </button>
        </div>
    </form>

    @if($hasSubmitted)
        <div class="cms-certificate-lookup__result mt-12" aria-live="polite">
            @if($lookupStatus === 'credentials_mismatch')
                <p class="cms-certificate-lookup__alert cms-certificate-lookup__alert--error">
                    {{ $lookupResult['message'] ?? '会员姓名或证书编号输入错误' }}
                </p>
            @elseif($lookupStatus === 'found')
                <div class="cms-certificate-lookup__result-card">
                    <h3 class="cms-certificate-lookup__result-title">查询结果</h3>
                    <dl class="cms-certificate-lookup__result-list">
                        <div class="cms-certificate-lookup__result-row">
                            <dt>持证人</dt>
                            <dd>{{ $lookupResult['full_name'] ?? '' }}</dd>
                        </div>
                        <div class="cms-certificate-lookup__result-row">
                            <dt>项目名称</dt>
                            <dd>{{ $lookupResult['project_names'] ?? '' }}</dd>
                        </div>
                        <div class="cms-certificate-lookup__result-row">
                            <dt>证书状态</dt>
                            <dd>有效</dd>
                        </div>
                        <div class="cms-certificate-lookup__result-row">
                            <dt>有效期</dt>
                            <dd>永久</dd>
                        </div>
                        <div class="cms-certificate-lookup__result-row">
                            <dt>查询时间</dt>
                            <dd>{{ optional($lookupResult['queried_at'] ?? null)->format('Y-m-d H:i:s') }}</dd>
                        </div>
                    </dl>

                    <div class="cms-certificate-lookup__notes">
                        <p>*本查询仅证明持证人已完成该项目学习并通过考核，获颁上述项目证书。不代表 IPA 会籍资格。</p>
                        <p>*如需正式核验，请致电 400-999-0590 联系 IPA 中国办公室。</p>
                    </div>
                </div>
            @else
                <div class="cms-certificate-lookup__result-card">
                    <h3 class="cms-certificate-lookup__result-title">查询结果</h3>
                    <p class="cms-certificate-lookup__not-found">未查询到相关证书信息</p>
                    <p class="cms-certificate-lookup__not-found-reason-title">可能原因：</p>
                    <ol class="cms-certificate-lookup__not-found-reasons">
                        <li>输入的姓名或会员编号有误</li>
                        <li>尚未获得该项目证书</li>
                    </ol>
                    <p class="cms-certificate-lookup__notes mt-6 mb-0">
                        如需进一步确认，请致电 400-999-0590 联系 IPA 中国办公室。
                    </p>
                </div>
            @endif
        </div>
    @endif
</div>
