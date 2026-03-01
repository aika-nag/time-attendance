<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Correction;
use App\Models\CorrectionBreak;
use App\Models\Attendance;
use Illuminate\Http\Request;
use App\Http\Requests\CorrectionRequest;
use Illuminate\Support\Facades\Auth;

class CorrectionController extends Controller
{
    public function show()
    {
        $unApprovedCorrections = Correction::where('user_id', Auth::id())->where('status', 1)->get();
        $approvedCorrections = Correction::where('user_id', Auth::id())->where('status', 2)->get();

        return view('request', compact('unApprovedCorrections', 'approvedCorrections'));
    }

    public function store(CorrectionRequest $request)
    {
        $attendance = Attendance::where('user_id', Auth::id())->where('date', $request->date)->first();

        $correction = new Correction();
        $correction->user_id = Auth::id();
        if($attendance){
            $correction->attendance_id = $attendance->id;
        }
        $correction->date = $request->date;
        $correction->start_time = $request->start_time;
        $correction->end_time = $request->end_time;
        $correction->reason = $request->reason;
        $correction->save();

        $correctionBreakStartTimes = $request->input('break_start_time');
        $correctionBreakEndTimes = $request->input('break_end_time');
        foreach($correctionBreakStartTimes as $index=> $correctionBreakStartTime){
            $correctionBreakEndTime = $correctionBreakEndTimes[$index] ?? null;

            if(!empty($correctionBreakStartTime)){
                $correctionBreak = new CorrectionBreak();
                $correctionBreak->correction_id = $correction->id;
                $correctionBreak->start_time = $correctionBreakStartTime;
                $correctionBreak->end_time = $correctionBreakEndTime;
                $correctionBreak->save();
            }
        }

        $attendance = $correction;
        if($attendance->attendance_id == null){
            return redirect()->route('detail', [
            'date' => Carbon::parse($request->date)->format('Y-m-d')
            ]);
        } else {
            return redirect()->route('detail', $correction->attendance_id);
        }
    }
}
