@extends('layouts.app')

@section('title', '会員登録')

@section('css')
<link rel="stylesheet" href="{{ asset('/css/authentication.css') }}">
@endsection

@section('content')

@include('components.simple-header')
<form action="/register" method="post" class="form">
    @csrf
    <h1 class="auth-title">会員登録</h1>
    <label for="name" class="input-label">名前</label>
    <input type="text" name="name" id="name" class="input">
    <div class="error">
        @error('name')
        {{ $message }}
        @enderror
    </div>
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
    <label for="password_confirmation" class="input-label">パスワード確認</label>
    <input type="password" name="password_confirmation" id="password_confirmation" class="input">
    <button class="submit-button">登録する</button>
    <a href="/login" class="page-link authentication">ログインはこちら</a>
</form>
@endsection
