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
}
