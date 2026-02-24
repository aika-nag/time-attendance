@extends('layouts.app')

@section('title', 'スタッフ一覧ページ')

@section('css')
<link rel="stylesheet" href="{{ asset('/css/list.css') }}">
@endsection

@section('content')

@include('components.admin-header')
<main class="time-attendance">
    @include('components.heading')
    <table class="attendance-table">
        <tr>
            <th>名前</th>
            <th>メールアドレス</th>
            <th>月次勤怠</th>
        </tr>
        @foreach($users as $user)
        <tr>
            <td>{{ $user->name }}</td>
            <td>{{ $user->email }}</td>
            <td><a href="/admin/attendance/staff/{{ $user->id }}" class="detail-link">詳細</td>
        </tr>
        @endforeach
    </table>
</main>
@endsection
