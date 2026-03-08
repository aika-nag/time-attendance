<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\BreakTime;
use App\Models\Attendance;

class BreakTimeController extends Controller
{
    public function store()
    {
        $attendance = Attendance::where('user_id', Auth::id())->whereDate('date', today())->whereNull('end_time')->first();
        $breakTime = new BreakTime();
        $breakTime->attendance_id = $attendance->id;
        $breakTime->start_time = now()->format('H:i:00');
        $breakTime->save();

        return redirect('/');
    }

    public function endBreakTime()
    {
        $attendance = Attendance::where('user_id', Auth::id())
            ->whereDate('date', today())
            ->whereNull('end_time')->first();
        if(!$attendance) {
            return redirect('/');
        }
        $breakTime = $attendance->breakTimes()
            ->whereNull('end_time')->first();
        $breakTime->update([
            'end_time' => now()->format('H:i:00')
        ]);

        return redirect('/');
    }
}
