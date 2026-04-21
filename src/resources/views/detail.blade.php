@extends('layouts.app')

@section('title', '勤怠詳細ページ')

@section('css')
<link rel="stylesheet" href="{{ asset('/css/detail.css') }}">
@endsection

@section('content')
@auth('admin')
@include('components.admin-header')
@elseauth
@include('components.header')
@endauth
<main class="time-attendance">
    @include('components.heading')
    @if($mode== "view")
    <x-detail-table
        :attendance="$displayData"
        :breakTimes="$breakTimes"
        mode="view" />
    <div class="button-area">
        <p class="error">＊承認待ちのため修正はできません</p>
    </div>
    @elseif($mode== "approve")
    <form action="{{ route('admin.approve', $displayData->id) }}" class="form" method="post">
        @csrf
        <x-detail-table
        :attendance="$displayData"
        :breakTimes="$breakTimes"
        mode="approve" />
        <div class="button-area">
            <button class="edit-button">承認</button>
        </div>
    </form>
    @elseif($mode== "approved")
        <x-detail-table
        :attendance="$displayData"
        :breakTimes="$breakTimes"
        mode="approved" />
        <div class="button-area">
            <button class="edit-button" disabled>承認済み</button>
        </div>
    @else
    @auth('admin')
    <form action="/admin/attendance/correction_requested" class="form" method="post">
    @else
    <form action="/attendance/detail/correction_requested" class="form" method="post">
    @endauth
        @csrf
        <input type="hidden" name="date" value="{{ $displayData->date }}">
        <input type="hidden" name="user_id" value="{{ $displayData->user_id}}">
        <x-detail-table
            :attendance="$displayData"
            :breakTimes="$breakTimes"
            mode="edit" />
        <div class="message-area">
            @foreach($errors->all() as $error)
            <li class="error">
                <ul>{{ $error }}</ul>
            </li>
            @endforeach
        </div>
        <div class="button-area">
            <button class="edit-button">修正</button>
        </div>
    </form>
    @endif
</main>
@endsection
