@extends('layouts.app')

@section('title', '申請一覧ページ')

@section('css')
<link rel="stylesheet" href="{{ asset('/css/request.css') }}">
@endsection

@section('content')
@auth('admin')
@include('components.admin-header')
@elseauth
@include('components.header')
@endauth
<main class="time-attendance">
    @include('components.heading')
    <div class="sort-request">
        <input type="radio" class="category unapproved"  name="category" id="unapproved" checked><label for="unapproved">承認待ち</label>
        <input type="radio" class="category approved" id="approved" name="category"><label for="approved">承認済み</label>
    </div>
    <div class="unapproved-request">
        @if($unApprovedCorrections->isNotEmpty())
        <table class="attendance-table">
            <tr>
                <th>状態</th>
                <th>名前</th>
                <th>対象日時</th>
                <th>申請理由</th>
                <th>申請日時</th>
                <th>詳細</th>
            </tr>
            @foreach($unApprovedCorrections as $unApprovedCorrection)
            <tr>
                <td>承認待ち</td>
                <td>{{ $unApprovedCorrection->user->name }}</td>
                <td>{{ $unApprovedCorrection->date->format('Y/m/d') }}</td>
                <td>{{ $unApprovedCorrection->reason }}</td>
                <td>{{ $unApprovedCorrection->created_at->format('Y/m/d') }}</td>
                <td>
                    @auth('admin')
                    <a href="{{ route('admin.request', $unApprovedCorrection->id) }}" class="detail-link">詳細</a>
                    @elseauth
                    @if($unApprovedCorrection->attendance_id == null)
                    <a href="{{ route('detail', ['date' => $unApprovedCorrection->date->format('Y-m-d')]) }}" class="detail-link">詳細</a>
                    @else
                    <a href="{{ route('detail', $unApprovedCorrection->attendance_id) }}" class="detail-link">詳細</a>
                    @endif
                    @endauth
                </td>
            </tr>
            @endforeach
        </table>
        @endif
    </div>
    <div class="approved-request">
        @if($approvedCorrections->isNotEmpty())
        <table class="attendance-table">
            <tr>
                <th>状態</th>
                <th>名前</th>
                <th>対象日時</th>
                <th>申請理由</th>
                <th>申請日時</th>
                <th>詳細</th>
            </tr>
            @foreach($approvedCorrections as $approvedCorrection)
            <tr>
                <td>承認済み</td>
                <td>{{ $approvedCorrection->user->name }}</td>
                <td>{{ $approvedCorrection->date->format('Y/m/d') }}</td>
                <td>{{ $approvedCorrection->reason }}</td>
                <td>{{ $approvedCorrection->created_at->format('Y/m/d') }}</td>
                <td>
                    @auth('admin')
                    <a href="{{ route('admin.detail', $approvedCorrection->attendance_id) }}" class="detail-link">詳細</a>
                    @elseauth
                    <a href="{{ route('detail', $approvedCorrection->attendance_id) }}" class="detail-link">詳細</a>
                    @endauth
                </td>
            </tr>
            @endforeach
        </table>
        @endif
    </div>
</main>
@endsection
