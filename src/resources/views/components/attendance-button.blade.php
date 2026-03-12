@if (isset($attendance))
    @if($attendance->start_time && !$attendance->end_time)
        @if(isset($breakTimeNow))
        <form action="/attendance/break-finish" method="post" class="attendance-form">
        @csrf
        <button class="count-button break">休憩戻</button>
        </form>
        @else
        <div class="flex">
            <form action="/attendance/finish" method="post" class="attendance-form">
            @csrf
            <button class="count-button">退勤</button>
            </form>
            <form action="/attendance/break-begin" method="post" class="attendance-form">
            @csrf
            <button class="count-button break">休憩入</button>
            </form>
        </div>
        @endif
    @elseif($attendance->start_time && $attendance->end_time)
        <p class="well-done-message">お疲れ様でした。</p>
    @endif
@else
<form action="/attendance/begin" method="post" class="attendance-form">
    @csrf
    <button class="count-button">出勤</button>
</form>
@endif
