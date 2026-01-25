<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Attendance;
use App\Models\BreakTime;
use Carbon\Carbon;
use PhpParser\Node\Stmt\Break_;

class AttendanceController extends Controller
{
    //
    public function index()
    {
        $attendance = Attendance::where('user_id', Auth::id())
            ->where('date', today())
            ->first();

        $breakTimeNow = BreakTime::where('user_id', Auth::id())
            ->where('date', today())
            ->where('end_time', null)
            ->first();

        return view('index', compact('attendance', 'breakTimeNow'));
    }

    public function store(Request $request)
    {
        $attendance = new Attendance();
        $attendance->user_id = Auth::id();
        $attendance->date = today();
        $attendance->start_time = Carbon::now();
        $attendance->save();

        return redirect('/');
    }

    public function endDay(Request $request)
    {
        $attendance = Attendance::where('user_id', Auth::id())
            ->where('date', today())
            ->first();
        $attendance->end_time = Carbon::now();
        $attendance->update();

        return redirect('/');
    }
}
