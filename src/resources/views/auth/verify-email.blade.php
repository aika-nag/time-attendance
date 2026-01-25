@extends('layouts.app')

@section('title', 'メール認証ページ')

@section('css')
<link rel="stylesheet" href="{{ asset('/css/email.css') }}">
@endsection

@section('content')

@include('components.simple-header')
<p class="notice-message">
    <span class="line-break">登録していただいた</span><span class="line-break">メールアドレスに</span><span class="line-break">認証メールを</span><span class="line-break">送付しました。</span><br />
    <span class="line-break">メール認証を</span><span class="line-break">完了してください。</span></p>
<div class="verify-button">
    <a href="http://localhost:8025/" class="verify-link">認証はこちらから</a>
</div>
<form action="/email/verification-notification" method="post">
    @csrf
    <button class="resend-button">認証メールを再送する</button>
</form>
@endsection
