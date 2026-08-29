@extends('layouts.member', ['bodyClass' => 'member-bolue-page'])

@section('title', '学习平台')

@section('content')
    <section class="member-bolue-message-section">
        <div class="member-bolue-message-card">
            <p class="member-bolue-message-card__text">{{ $message }}</p>
            <p class="member-bolue-message-card__actions">
                <a href="{{ route('member.dashboard') }}">返回会员中心</a>
                <span aria-hidden="true"> · </span>
                <a href="{{ route('home') }}">返回首页。</a>
            </p>
        </div>
    </section>
@endsection
