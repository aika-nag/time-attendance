<table class="attendance-table">
    <tr>
        <th>日付</th>
        <th>出勤</th>
        <th>退勤</th>
        <th>休憩</th>
        <th>合計</th>
        <th>詳細</th>
    </tr>
    @foreach($monthDayLists as $day)
    @php
    $attendance = $attendances[$day->toDateString()] ?? null;
    @endphp
    <tr>
        <td>{{ $day->format('m/d') }}({{ ['日', '月', '火', '水', '木', '金', '土'][$day->dayOfWeek] }})</td>
        <td>{{ $attendance?->start_time?->format('H:i') }}</td>
        <td>{{ $attendance?->end_time?->format('H:i') }}</td>
        <td>{{ $attendance?->total_break_time }}</td>
        {{-- 休憩時間と合計勤務時間はアクセサを利用--}}
        <td>{{ $attendance?->total_work_time }}</td>
        @auth('admin')
        @if($attendance)
        <td><a href="/admin/attendance/{{ $attendance?->id }}" class="detail-link">詳細</a></td>
        @else
        <td>詳細</td>
        @endif
        @endauth
        @auth('web')
        @if($attendance)
        <td><a href="{{ route('detail', $attendance->id) }}" class="detail-link">詳細</a></td>
        @else
        <td><a href="{{ route('detail', ['date' => $day->format('Y-m-d')]) }}" class="detail-link">詳細</a></td>
        @endif
        @endauth
    </tr>
    @endforeach
</table>
