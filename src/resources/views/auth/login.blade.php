@extends('layouts.app')

@section('title', 'ログインページ')

@section('css')
<link rel="stylesheet" href="{{ asset('/css/authentication.css') }}">
@endsection

@section('content')

@include('components.simple-header')
<form action="/login" method="post" class="form">
    @csrf
    <h1 class="auth-title">ログイン</h1>
    <label for="email" class="input-label">メールアドレス</label>
    <input type="email" name="email" id="email" class="input">
    <div class="error">
        @error('email')
        {{ $message }}
        @enderror
    </div>
    <label for="password" class="input-label">パスワード</label>
    <input type="password" name="password" id="password" class="input">
    <div class="error">
        @error('password')
        {{ $message }}
        @enderror
    </div>
    <button class="submit-button">ログインする</button>
    <a href="/register" class="page-link authentication">会員登録はこちら</a>
</form>
@endsection
