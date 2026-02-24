<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Attendance;
use App\Models\BreakTime;
use App\Models\Correction;
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

        if($attendance->break_minutes === null){
            $attendance->break_minutes = 0;
        }
        $attendance->update();


        return redirect('/');
    }

    public function show(Request $request)
    {
        $targetDate = $request->date;
        if($request->has('date')){
            $targetDate = Carbon::createFromFormat('Y-m', $request->date);
        }else{
            $targetDate = Carbon::now();
        }

        $year = $targetDate->year;
        $month = $targetDate->month;
        $firstDay = $targetDate->copy()->firstOfMonth()->day;
        $lastDay = $targetDate->copy()->endOfMonth()->day;
        $attendances = Attendance::where('user_id', Auth::id())->whereYear('date', $year)->whereMonth('date', $month)->get()->keyBy(fn ($attendance) =>
        $attendance->date->toDateString());
        $monthDayLists = [];
        for ($day = $firstDay ;$day <= $lastDay;$day++) {
            $monthDayLists[] = Carbon::create($year, $month, $day);
        }

        $prevMonth = $targetDate->copy()->subMonth()->format('Y-m');
        $nextMonth = $targetDate->copy()->addMonth()->format('Y-m');
        return view('list', compact('attendances','monthDayLists', 'targetDate', 'prevMonth', 'nextMonth'));
    }

    public function detail(Attendance $attendance, Request $request)
    {
        if(!$attendance->exists){
            $attendance->user_id = Auth::id();
            $attendance->date = $request->date;
            $breakTimes = collect();
            $unapprovedCorrectionExists = null;
        } else {
        $breakTimes = BreakTime::where('user_id', Auth::id())->where('date', $attendance->date)->whereNotNull('end_time')->get();

        $unapprovedCorrectionExists = Correction::where('attendance_id', $attendance->id)->where('status', 1)->first();
        }
        return view('detail', compact('attendance', 'breakTimes', 'unapprovedCorrectionExists'));
    }
}
