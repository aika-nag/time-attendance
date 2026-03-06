<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pipeline\Pipeline;
use App\Actions\Admin\AttemptToAuthenticate;
use Laravel\Fortify\Actions\PrepareAuthenticatedSession;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\Auth\StatefulGuard;
use App\Responses\AdminLoginResponse;
use App\Responses\AdminLogoutResponse;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use App\Models\Attendance;
use App\Models\BreakTime;
use App\Models\Correction;
use App\Models\CorrectionBreak;
use NunoMaduro\Collision\Adapters\Phpunit\State;

class AdminController extends Controller
{
    protected $guard;

    public function __construct(StatefulGuard $guard)
    {
        $this->guard = $guard;
    }

    public function create()
    {
        return view('admin.login', ['guard' => 'admin']);
    }

    public function store(LoginRequest $request)
    {
        return $this->loginPipeline($request)->then(function($request) {
            return app(AdminLoginResponse::class);
        });
    }

    protected function loginPipeline(LoginRequest $request)
    {
        return (new Pipeline(app()))->send($request)->through(array_filter([
                AttemptToAuthenticate::class,
                PrepareAuthenticatedSession::class,
        ]));
    }

    public function index(Request $request)
    {
        if($request->has('date')){
            $targetDate = Carbon::createFromFormat('Y-m-d', $request->date);
        }else{
            $targetDate = Carbon::now();
        }
        $attendances = Attendance::whereDate('date', $targetDate)->get();
        $prevDay = $targetDate->copy()->subDay()->format('Y-m-d');
        $nextDay = $targetDate->copy()->addDay()->format('Y-m-d');

        return view('list', compact('attendances', 'targetDate', 'prevDay', 'nextDay'));
    }

    public function destroy(Request $request): AdminLogoutResponse
    {
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return  app(AdminLogoutResponse::class);
    }

    public function showStaff()
    {
        $users = User::all();

        return view('admin.staff', compact('users'));
    }

    public function showAttendance(User $user, Request $request)
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
        $attendances = Attendance::where('user_id', $user->id)->whereYear('date', $year)->whereMonth('date', $month)->get()->keyBy(fn ($attendance) =>
        $attendance->date->toDateString());
        $monthDayLists = [];
        for ($day = $firstDay ;$day <= $lastDay;$day++) {
            $monthDayLists[] = Carbon::create($year, $month, $day);
        }
        $prevMonth = $targetDate->copy()->subMonth()->format('Y-m');
        $nextMonth = $targetDate->copy()->addMonth()->format('Y-m');

        return view('admin.attendance', compact('attendances','monthDayLists', 'targetDate', 'prevMonth', 'nextMonth', 'user'));
    }

    public function detail(Attendance $attendance, Request $request)
    {
        if($attendance->exists){
            $date = $attendance->date;
            $unApprovedCorrection = $attendance->corrections()->where('status', 1)->with('correctionBreaks')->first();
        } else {
            $date = $request->date;
            $unApprovedCorrection = Correction::where('user_id', $request->user_id)->where('date', $request->date)->where('status', 1)->with('correctionBreaks')->first();
        }
        if($unApprovedCorrection){
            $displayData = $unApprovedCorrection;
            $breakTimes = $unApprovedCorrection->correctionBreaks;
            $mode = "approve";
        } elseif($attendance->exists){
            $displayData = $attendance;
            $breakTimes = $attendance->breakTimes;
            $mode = "edit";
        } else{
            $displayData = new Attendance([
                'user_id' => $request->user_id,
                'date' => $date
            ]);
            $breakTimes = collect();
            $mode = "edit";
        }

        return view('detail', compact('displayData', 'breakTimes', 'mode'));
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

    public function approve(Correction $correction)
    {
        $breakTimes = $correction->CorrectionBreaks;
        if($correction->attendance_id == null){
            $attendance = new Attendance();
            $attendance->user_id = $correction->user_id;
            $attendance->date = $correction->date;
            $attendance->start_time = $correction->start_time;
            $attendance->end_time = $correction->end_time;
            $attendance->save();

            $correction->update([
                'attendance_id' => $attendance->id
            ]);
        } else {
            $attendance = $correction->attendance;
            $attendance->update([
                'start_time' => $correction->start_time,
                'end_time' => $correction->end_time
            ]);
            $attendance->breakTimes()->delete();
        }
        foreach($breakTimes as $breakTime){
            $attendance->breakTimes()->create([
                'user_id' => $attendance->user_id,
                'start_time' => $breakTime->start_time,
                'end_time' => $breakTime->end_time
            ]);
        }
        $correction->update([
            'status' => 2
        ]);

        return redirect()->route('admin.request', $correction->id);
    }
}
