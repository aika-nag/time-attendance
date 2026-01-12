@extends('layouts.app')

@section('title', '勤怠一覧ページ')

@section('css')
<link rel="stylesheet" href="{{ asset('/css/list.css') }}">
@endsection

@section('content')

@include('components.header')
<p>勤怠一覧画面</p>
@endsection
