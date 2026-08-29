@extends('layouts.member', ['bodyClass' => 'member-bolue-page'])

@section('title', '学习账号')

@section('content')
    <section class="member-bolue-message-section">
        <div class="member-bolue-message-card">
            <p class="member-bolue-message-card__text">
                非常抱歉，未找到您的专属学习账号！<br>
                温馨提醒：<br>
                IPA资格有效会员首次登录学习，请发送邮件主题为<strong>“会员编号+姓名+申请开通IPA专属铂略学习账号”</strong>邮件至china.service@ipaau.org.cn ，申请专属学习账号。如需更新预留手机号码或有其他疑问请致电4009990590联系IPA工作人员获取帮助。
            </p>
            <p class="member-bolue-message-card__actions">
                <a href="{{ route('home') }}">返回首页。</a>
            </p>
        </div>
    </section>
@endsection
