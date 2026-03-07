<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Attendance;
use App\Models\BreakTime;
use App\Models\Correction;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    //
    public function index()
    {
        $attendance = Attendance::where('user_id', Auth::id())
            ->where('date', today())
            ->first();
        if($attendance){
            $breakTimeNow = BreakTime::where('user_id', Auth::id())
                ->where('attendance_id', $attendance->id)
                ->where('end_time', null)
                ->first();
            return view('index', compact('attendance', 'breakTimeNow'));
        }

        return view('index', compact('attendance'));
    }

    public function store()
    {
        $attendance = new Attendance();
        $attendance->user_id = Auth::id();
        $attendance->date = today();
        $attendance->start_time = now()->format('H:i:00');
        $attendance->save();

        return redirect('/');
    }

    public function endDay()
    {
        $attendance = Attendance::where('user_id', Auth::id())
            ->where('date', today())
            ->whereNull('end_time')
            ->first();
        $attendance->update([
            'end_time' => now()->format('H:i:00')
        ]);

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
        if($attendance->exists) {
            $date = $attendance->date;
        } else {
            $date = $request->date;
        }
        $unApprovedCorrection = Correction::where('user_id', Auth::id())
            ->where('date', $date)
            ->where('status', 1)
            ->with('correctionBreaks')->first();
        if($unApprovedCorrection){
            $displayData = $unApprovedCorrection;
            $breakTimes = $unApprovedCorrection->correctionBreaks;
            $mode = "view";
        } elseif($attendance->exists){
            $displayData = $attendance;
            $breakTimes = $attendance->breakTimes;
            $mode = "edit";
        } else {
            $displayData = new Attendance([
                'user_id' => Auth::id(),
                'date' => $date
            ]);
            $breakTimes = collect();
            $mode = "edit";
        }

        return view('detail', compact('displayData', 'breakTimes', 'mode'));
    }
}
