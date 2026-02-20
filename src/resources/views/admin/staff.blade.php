@extends('layouts.app')

@section('title', 'スタッフ一覧ページ')

@section('css')
<link rel="stylesheet" href="{{ asset('/css/list.css') }}">
@endsection

@section('content')

@include('components.admin-header')
@include('components.heading')
<main class="time-attendance">
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
            <td>詳細</td>
        </tr>
        @endforeach
    </table>
</main>
@endsection
