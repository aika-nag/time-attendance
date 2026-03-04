@extends('layouts.app')

@section('title', '勤怠一覧ページ')

@section('css')
<link rel="stylesheet" href="{{ asset('/css/list.css') }}">
@endsection

@section('content')
@auth('admin')
@include('components.admin-header')
<main class="time-attendance">
    @include('components.heading')
    <div class="calendar-head">
        <a href="{{ url('/admin/attendance/list?date='. $prevDay) }}" class="previous-link">前日</a>
        <p class="selected-date">{{ $targetDate->format('Y/m/d') }}</p>
        <a href="{{ url('/admin/attendance/list?date='. $nextDay) }}" class="next-link">翌日</a>
    </div>
    <table class="attendance-table">
        <tr>
            <th>名前</th>
            <th>出勤</th>
            <th>退勤</th>
            <th>休憩</th>
            <th>合計</th>
            <th>詳細</th>
        </tr>
        @foreach($attendances as $attendance)
        <tr>
            <td>{{ $attendance->user->name }}</td>
            <td>{{ $attendance?->start_time->format('H:i') }}</td>
            <td>{{ $attendance?->end_time?->format('H:i') }}</td>
            <td>{{ $attendance?->total_break_time }}</td>
            <td>{{ $attendance?->total_work_time }}</td>
            <td><a href="/admin/attendance/{{ $attendance->id}}" class="detail-link">詳細</a></td>
        </tr>
        @endforeach
    </table>
</main>
@endauth

@auth('web')
@include('components.header')
<main class="time-attendance">
    @include('components.heading')
    <div class="calendar-head">
        <a href="{{ url('/attendance/list?date='. $prevMonth) }}" class="previous-link">前月</a>
        <p class="selected-date">{{ $targetDate->format('Y/m') }}</p>
        <a href="{{ url('/attendance/list?date='. $nextMonth) }}" class="next-link">翌月</a>
    </div>
    @include('components.attendance-table')
</main>
@endauth
@endsection
