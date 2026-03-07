<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Correction;
use App\Models\Attendance;
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

    public function showByAdmin()
    {
        $unApprovedCorrections = Correction::where('status', 1)->get();
        $approvedCorrections = Correction::where('status', 2)->get();

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
                $correction->correctionBreaks()->create([
                'start_time' => $correctionBreakStartTime,
                'end_time' => $correctionBreakEndTime
            ]);
            }
        }
        if($correction->attendance_id == null){
            return redirect()->route('detail', [
            'date' => Carbon::parse($request->date)->format('Y-m-d')
            ]);
        } else {
            return redirect()->route('detail', $correction->attendance_id);
        }
    }

    public function storeByAdmin(CorrectionRequest $request)
    {
        $attendance = Attendance::where('user_id', $request->user_id)->where('date', $request->date)->first();
        $correction = new Correction();
        $correction->user_id = $request->user_id;
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
                $correction->correctionBreaks()->create([
                    'start_time' => $correctionBreakStartTime,
                    'end_time' => $correctionBreakEndTime
                ]);
            }
        }
        if($correction->attendance_id == null){
            return redirect()->route('admin.request', $correction->id);
        } else {
            return redirect()->route('admin.detail', $correction->attendance_id);
        }
    }

    public function showCorrectionDetail(Correction $correction)
    {
        $breakTimes = $correction->correctionBreaks;
        if($correction->status == 1){
            $mode = "approve";
        } elseif($correction->status == 2){
            $mode = "approved";
        }
        $data = [
            'displayData' => $correction,
            'breakTimes' => $breakTimes,
            'mode' => $mode,
        ];

        return view('detail', $data);
    }
}
