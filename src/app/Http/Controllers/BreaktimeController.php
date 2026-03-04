<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\BreakTime;
use App\Models\Attendance;
use Carbon\Carbon;

class BreakTimeController extends Controller
{
    public function store()
    {
        $attendance = Attendance::where('user_id', Auth::id())->whereDate('date', today())->whereNull('end_time')->first();
        $breakTime = new BreakTime();
        $breakTime->user_id = Auth::id();
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
