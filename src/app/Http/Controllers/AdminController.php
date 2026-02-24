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

    public function detail(Attendance $attendance)
    {
        $breakTimes = BreakTime::where('user_id', $attendance->user_id)->where('date', $attendance->date)->whereNotNull('end_time')->get();
        $unapprovedCorrectionExists = Correction::where('attendance_id', $attendance->id)->where('status', 1)->first();
        $data = [
            'attendance' => $attendance,
            'breakTimes' => $breakTimes,
            'unapprovedCorrectionExists' => $unapprovedCorrectionExists
        ];

        return view('detail', $data);
    }

    public function showCorrection()
    {
        $unApprovedCorrections = Correction::where('status', 1)->get();
        $approvedCorrections = Correction::where('status', 2)->get();

        return view('request', compact('unApprovedCorrections', 'approvedCorrections'));
    }
}
