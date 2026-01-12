@extends('layouts.app')

@section('title', 'メール認証ページ')

@section('css')
<link rel="stylesheet" href="{{ asset('/css/email.css') }}">
@endsection

@section('content')

@include('components.simple-header')
<p class="notice-message">登録していただいたメールアドレスに認証メールを送付しました。<br />
メール認証を完了してください。</p>
<div class="verify-button">
    <a href="http://localhost:8025/" class="verify-link">認証はこちらから</a>
</div>
<form action="/email/verification-notification" method="post">
    @csrf
    <button class="resend-button">認証メールを再送する</button>
</form>
@endsection
