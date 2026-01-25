<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\BreakTime;
use App\Models\Attendance;
use Carbon\Carbon;

class BreakTimeController extends Controller
{
    public function store(Request $request)
    {
        $breakTime = new BreakTime();
        $breakTime->user_id = Auth::id();
        $breakTime->date = today();
        $breakTime->start_time = Carbon::now();
        $breakTime->save();

        return redirect('/');
    }

    public function endBreakTime(Request $request)
    {
        $breakTime = BreakTime::where('user_id', Auth::id())
            ->where('date', today())
            ->whereNull('end_time')
            ->first()
            ->update([
            'end_time' => Carbon::now()
        ]);

        $breakTimes = BreakTime::where('user_id', Auth::id())
            ->where('date', today())
            ->whereNotNull('end_time')
            ->get();

        $totalBreakTime = 0;
        foreach($breakTimes as $breakTime){
            $diff = $breakTime->end_time->diffInMinutes($breakTime->start_time);
            $totalBreakTime += $diff;
        }
        $attendance = Attendance::where('user_id', Auth::id())
            ->where('date', today())
            ->whereNull('end_time')
            ->first();

        $attendance->update([
            'break_time' => $totalBreakTime
        ]);
        
        return redirect('/');
    }
}
