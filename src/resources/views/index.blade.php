@extends('layouts.app')

@section('title', 'トップページ')

@section('css')
<link rel="stylesheet" href="{{ asset('/css/index.css') }}">
@endsection

@section('content')

@include('components.header')
<main class="time-attendance">
    @if (isset($attendance))
        @if($attendance->start_time && !$attendance->end_time)
            @if(isset($breakTimeNow))
            <p class="user-status">休憩中</p>
            @else
            <p class="user-status">出勤中</p>
            @endif
        @elseif($attendance->start_time && $attendance->end_time)
            <p class="user-status">退勤済</p>
        @endif
    @else
    <p class="user-status">勤務外</p>
    @endif
    <p class="date">{{ today()->format('Y年n月j日') }}{{ today()->isoFormat('(ddd)') }}</p>
    <p class="time">{{ now()->format('H:i') }}</p>
    @include('components.attendance-button')
</main>
@endsection
