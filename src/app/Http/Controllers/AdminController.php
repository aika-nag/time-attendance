<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pipeline\Pipeline;
use App\Actions\Admin\AttemptToAuthenticate;
use Laravel\Fortify\Actions\PrepareAuthenticatedSession;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\Auth\StatefulGuard;
use App\Responses\AdminLoginResponse;
use App\Responses\AdminLogoutResponse;
use App\Http\Requests\LoginRequest;
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

    public function index()
    {
        return view('list');
    }

    public function destroy(Request $request): AdminLogoutResponse
    {
        Auth::guard('admin')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return  app(AdminLogoutResponse::class);
    }
}
