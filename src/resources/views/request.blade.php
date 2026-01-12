@extends('layouts.app')

@section('title', '申請一覧ページ')

@section('css')
<link rel="stylesheet" href="{{ asset('/css/request.css') }}">
@endsection

@section('content')

@include('components.header')
<p>打刻画面</p>
@endsection
