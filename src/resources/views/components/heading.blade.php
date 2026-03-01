@auth('admin')
@if(Request::is('admin/attendance/list'))
<h1 class="page-title">{{ $targetDate->format('Y年n月j日') }}の勤怠</h1>
@elseif(Request::is('admin/staff/list'))
<h1 class="page-title">スタッフ一覧</h1>
@elseif(Route::is('admin.attendance'))
<h1 class="page-title">{{ $user->name }}さんの勤怠</h1>
@elseif(Route::is('admin.detail')||Route::is('admin.request'))
<h1 class="page-title">勤怠詳細</h1>
@elseif(Route::is('admin.correction'))
<h1 class="page-title">申請一覧</h1>
@endif
@endauth

@if(Request::is('attendance/list'))
<h1 class="page-title">勤怠一覧</h1>
@elseif(Route::is('detail'))
<h1 class="page-title">勤怠詳細</h1>
@elseif(Route::is('correction'))
<h1 class="page-title">申請一覧</h1>
@endif


