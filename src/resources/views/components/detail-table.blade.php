@props([
    'attendance',
    'breakTimes',
    'mode'
])

<table class="detail-table">
    <tr>
        <th>名前</th>
        <td colspan="3">{{ $attendance->user->name }}</td>
    </tr>
    <tr>
        <th>日付</th>
        <td>{{ $attendance->date->format('Y年') }}</td>
        <td></td>
        <td>{{ $attendance->date->format('n月j日') }}</td>
    </tr>
    <tr>
        <th>出勤・退勤</th>
        <td>
            @if($mode== "approve")
            <input type="time" value="{{  $attendance->start_time }}" class="input" name="start_time" readonly>
            @elseif($mode== "edit")
            <input type="time" value="{{ old('start_time', $attendance->start_time?->format('H:i')) }}" class="input" name="start_time">
            @else
            <input type="time" value="{{ $attendance->start_time }}" class="input" name="start_time" readonly>
            @endif
        </td>
        <td>〜</td>
        <td>
            @if($mode== "approve")
            <input type="time" value="{{ $attendance?->end_time }}" class="input" name="end_time" readonly>
            @elseif($mode== "edit")
            <input type="time" value="{{ old('end_time', $attendance?->end_time?->format('H:i')) }}" class="input" name="end_time">
            @else
            <input type="time" value="{{ $attendance?->end_time }}" class="input" name="end_time" readonly>
            @endif
        </td>
    </tr>
    @if($breakTimes->isEmpty()||$breakTimes == null)
    <tr>
        <th>休憩</th>
        <td>
            <input type="time" value="{{ old('break_start_time.0') }}" class="input" name="break_start_time[]" {{ $mode !== 'edit' ? 'readonly': '' }}>
        </td>
        <td>〜</td>
        <td>
            <input type="time" value="{{ old('break_end_time.0') }}" class="input" name="break_end_time[]" {{ $mode !== 'edit' ? 'readonly': '' }}>
        </td>
    </tr>
    @else
    @foreach($breakTimes as $breakTime)
    <tr>
        <th>休憩{{ $loop->iteration }}</th>
        <td>
            <input type="time" value="{{ old('break_start_time.$loop->index',  $breakTime->start_time) }}" class="input" name="break_start_time[]" {{ $mode !== 'edit' ? 'readonly': '' }}>
        </td>
        <td>〜</td>
        <td>
            <input type="time" value="{{ old('break_end_time.$loop->index', $breakTime->end_time) }}" class="input" name="break_end_time[]" {{ $mode !== 'edit' ? 'readonly': '' }}>
        </td>
    </tr>
    @endforeach
    <tr>
        <th>休憩{{ $breakTimes->count() +1 }}</th>
        <td>
            <input type="time" class="input" name="break_start_time[]" {{ $mode !== 'edit' ? 'readonly': '' }}>
        </td>
        <td>〜</td>
        <td>
            <input type="time" class="input" name="break_end_time[]" {{ $mode !== 'edit' ? 'readonly': '' }}>
        </td>
    </tr>
    @endif
    <tr>
        <th><label for="reason">備考</label></th>
        <td colspan="3">
            @if($mode== "edit")
            <textarea name="reason" id="reason" class="textarea"></textarea>
            @else
            <textarea name="reason" id="reason" class="textarea" readonly>{{ $attendance->reason }}</textarea>
            @endif
        </td>
    </tr>
</table>
