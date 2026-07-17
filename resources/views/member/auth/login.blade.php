@extends('layouts.member', ['bodyClass' => 'member-login-page'])

@section('title', '会员登录')

@section('content')
    <section class="member-login-section">
        <div class="member-login-card">
            <h1 class="member-login-card__title font-apex-book">会员登录</h1>
            <p class="member-login-card__subtitle">使用注册手机号接收验证码登录会员中心</p>

            @if ($errors->any())
                <div class="member-alert member-alert--error" role="alert">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('member.verify') }}" class="member-login-form" id="member-login-form">
                @csrf

                <label class="member-field" for="mobile">
                    <span class="member-field__label">手机号</span>
                    <input
                        id="mobile"
                        name="mobile"
                        type="tel"
                        inputmode="numeric"
                        autocomplete="tel"
                        placeholder="请输入11位手机号"
                        value="{{ old('mobile') }}"
                        required
                        class="member-field__input"
                    />
                </label>

                <label class="member-field" for="code">
                    <span class="member-field__label">验证码</span>
                    <div class="member-field__inline">
                        <input
                            id="code"
                            name="code"
                            type="text"
                            inputmode="numeric"
                            maxlength="6"
                            placeholder="6位验证码"
                            required
                            class="member-field__input"
                        />
                        <button type="button" class="member-btn member-btn--secondary" id="send-code-btn">
                            获取验证码
                        </button>
                    </div>
                </label>

                <p class="member-login-hint" id="send-code-message" aria-live="polite"></p>

                <button type="submit" class="member-btn member-btn--primary member-login-form__submit">
                    登录
                </button>
            </form>
        </div>
    </section>
@endsection

@push('scripts')
    <script src="{{ asset('assets/js/member-portal.js') }}" defer></script>
@endpush
