@extends('layouts.app')

@section('title', '勤怠詳細ページ')

@section('css')
<link rel="stylesheet" href="{{ asset('/css/detail.css') }}">
@endsection

@section('content')
@auth
@include('components.admin-header')
@else
@include('components.header')
@endauth
<p>打刻画面</p>
@endsection
