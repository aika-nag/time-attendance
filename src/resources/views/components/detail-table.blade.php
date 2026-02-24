@if($unapprovedCorrectionExists)
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
        <td><input type="time" value="{{ $attendance->start_time->format('H:i') }}" class="input" name="start_time" readonly></td>
        <td>〜</td>
        <td><input type="time" value="{{ $attendance?->end_time?->format('H:i') }}" class="input" name="end_time" readonly></td>
    </tr>
    @if($breakTimes->isEmpty())
    <tr>
        <th>休憩</th>
        <td><input type="time" class="input" readonly></td>
        <td>〜</td>
        <td><input type="time" class="input" readonly></td>
    </tr>
    @else
    @foreach($breakTimes as $breakTime)
    <tr>
        <th>休憩{{ $loop->iteration }}</th>
        <td><input type="time" value="{{ $breakTime->start_time->format('H:i') }}" class="input" readonly></td>
        <td>〜</td>
        <td><input type="time" value="{{ $breakTime->end_time->format('H:i') }}" class="input" readonly></td>
    </tr>
    @php
    if($loop->last){
    $count = ++$loop->iteration;
    }
    @endphp
    @endforeach
    <tr>
        <th>休憩{{ $count }}</th>
        <td><input type="time" class="input" readonly></td>
        <td>〜</td>
        <td><input type="time" class="input" readonly></td>
    </tr>
    @endif
    <tr>
        <th><label for="reason">備考</label></th>
        <td colspan="3"><textarea name="reason" id="reason" class="textarea" readonly></textarea></td>
    </tr>
</table>
<div class="button-area">
    <p class="error">＊承認待ちのため修正はできません</p>
</div>
@else
<table class="detail-table">
    <tr>
        <th>名前</th>
        <td colspan="3">{{ auth()->user()->name }}</td>
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
            <input type="time" value="{{ old('start_time', $attendance->start_time?->format('H:i')) }}" class="input" name="start_time">
        </td>
        <td>〜</td>
        <td><input type="time" value="{{ old('end_time', $attendance?->end_time?->format('H:i')) }}" class="input" name="end_time"></td>
    </tr>
    @if($breakTimes->isEmpty())
    <tr>
        <th>休憩</th>
        <td><input type="time" class="input" name="break_start_time[]" value="{{ old('break_start_time'. 0) }}"></td>
        <td>〜</td>
        <td><input type="time" class="input" name="break_end_time[]" value="{{ old('break_end_time'. 0) }}"></td>
    </tr>
    @else
    @foreach($breakTimes as $breakTime)
    <tr>
        <th>休憩{{ $loop->iteration }}</th>
        <td><input type="time" value="{{ old('break_start_time'. $loop->index,  $breakTime->start_time?->format('H:i')) }}" class="input" name="break_start_time[]"></td>
        <td>〜</td>
        <td><input type="time" value="{{ old('break_end_time'. $loop->index, $breakTime->end_time?->format('H:i')) }}" class="input" name="break_end_time[]"></td>
    </tr>
    @endforeach
    <tr>
        <th>休憩{{ $breakTimes->count() + 1 }}</th>
        <td><input type="time" class="input" name="break_start_time[]"></td>
        <td>〜</td>
        <td><input type="time" class="input" name="break_end_time[]"></td>
    </tr>
    @endif
    <tr>
        <th><label for="reason">備考</label></th>
        <td colspan="3">
            <textarea name="reason" id="reason" class="textarea"></textarea>
        </td>
    </tr>
</table>
<div class="message-area">
    @error('end_time')
    <p class="error">{{ $errors->first('end_time') }}</p>
    @enderror
    @error('break_start_time')
    <p class="error">{{ $errors->first('break_start_time') }}</p>
    @enderror
    @error('break_end_time')
    <p class="error">{{ $errors->first('break_end_time') }}</p>
    @enderror
    @error('reason')
    <p class="error">{{ $errors->first('reason') }}</p>
    @enderror
</div>
<div class="button-area">
    <button class="edit-button">修正</button>
</div>
@endif
