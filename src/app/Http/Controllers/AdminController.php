<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
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
use App\Models\Correction;

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

    public function exportCSV(Request $request)
    {
        $user = User::find($request->user);
        $year = Carbon::parse($request->day)->year;
        $month = Carbon::parse($request->day)->month;
        $attendances = $user->attendances()->whereYear('date', $year)->whereMonth('date', $month)->get();
        $stream = fopen('php://temp', 'w');
        $csvHeader = array('名前', '日付', '出勤', '退勤', '休憩', '合計');
        fputcsv($stream, $csvHeader);
        foreach($attendances as $attendance)
            {
                $row = array(
                    '名前' =>  $attendance->user->name,
                    '日付' => $attendance->date->format('Y-m-d'),
                    '出勤' => $attendance->start_time->format('H:i'),
                    '退勤' => $attendance->end_time->format('H:i'),
                    '休憩' => $attendance->total_break_time,
                    '合計' => $attendance->total_work_time
                );
                fputcsv($stream, $row);
            }
        rewind($stream);
        $csv = stream_get_contents($stream);
        $csv = mb_convert_encoding($csv, 'sjis-win', 'UTF-8');
        fclose($stream);
        $headers = array(
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=attendance.csv'
        );

        return Response::make($csv, 200, $headers);
    }
}
