@extends('layouts.app')

@section('title', '勤怠詳細ページ')

@section('css')
<link rel="stylesheet" href="{{ asset('/css/detail.css') }}">
@endsection

@section('content')

@include('components.header')
<p>打刻画面</p>
@endsection
