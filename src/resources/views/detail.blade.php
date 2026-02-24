@extends('layouts.app')

@section('title', '勤怠詳細ページ')

@section('css')
<link rel="stylesheet" href="{{ asset('/css/detail.css') }}">
@endsection

@section('content')
@auth('admin')
@include('components.admin-header')
<main class="time-attendance">
    <form action="">
        @include('components.heading')
        @include('components.detail-table')
    </form>
</main>
@elseauth
@include('components.header')
<main class="time-attendance">
    <form action="/attendance/detail/{{ $attendance->id }}" class="form" method="post">
        @csrf
        @include('components.heading')
        @include('components.detail-table')
    </form>
</main>
@endauth
@endsection
