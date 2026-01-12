@extends('layouts.app')

@section('title', 'トップページ')

@section('css')
<link rel="stylesheet" href="{{ asset('/css/index.css') }}">
@endsection

@section('content')

@include('components.header')
<main class="time-attendance">
    <p class="user-status">状態</p>
    <p class="date">{{ today()->format('Y年n月j日') }}{{ today()->isoFormat('(ddd)') }}</p>
    <p class="time">{{ now()->format('H:i') }}</p>
    <form action="">
        <button class="count-button">出勤</button>
    </form>
</main>
@endsection
