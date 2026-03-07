@extends('layouts.app')

@section('title', '勤怠一覧ページ')

@section('css')
<link rel="stylesheet" href="{{ asset('/css/list.css') }}">
@endsection

@section('content')
@include('components.admin-header')
<main class="time-attendance">
    @include('components.heading')
    <div class="calendar-head">
        <a href="{{ route('admin.attendance', ['user' => $user->id,
        'date' => $prevMonth]) }}" class="previous-link">前月</a>
        <p class="selected-date">{{ $targetDate->format('Y/m') }}</p>
        <a href="{{ route('admin.attendance', ['user' => $user->id,
        'date' => $nextMonth]) }}" class="next-link">翌月</a>
    </div>
    @include('components.attendance-table')
    <form action="/admin/csv/export" class="button-area">
        <button class="edit-button">CSV出力</button>
        <input type="hidden" name="user" value="{{ $user->id }}">
        <input type="hidden" name="day" value="{{ $targetDate }}" >
    </form>
</main>
@endsection
