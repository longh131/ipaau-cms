@extends('layouts.member', ['bodyClass' => 'member-bolue-page'])

@section('title', '学习账号')

@section('content')
    <section class="member-bolue-message-section">
        <div class="member-bolue-message-card">
            <p class="member-bolue-message-card__text">
                您的会员资料中尚未预留手机号，暂时无法进入学习平台。如需更新预留手机号码或有其他疑问请致电 4009990590 联系 IPA 工作人员获取帮助。
            </p>
            <p class="member-bolue-message-card__actions">
                <a href="{{ route('member.profile') }}">查看我的信息</a>
                <span aria-hidden="true"> · </span>
                <a href="{{ route('home') }}">返回首页。</a>
            </p>
        </div>
    </section>
@endsection
