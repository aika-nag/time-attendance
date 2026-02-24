<?php

namespace App\Http\Controllers;

use App\Models\Correction;
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

    public function edit(CorrectionRequest $request, Attendance $attendance)
    {
        $correction = new Correction();
        $correction->user_id = Auth::id();
        $correction->attendance_id = $attendance->id;
        $correction->start_time = $request->start_time;
        $correction->end_time = $request->end_time;
        $correction->reason = $request->reason;
        $correction->date = $attendance->date;
        $correction->save();

        return redirect()->route('detail', $attendance->id);

    }
}
