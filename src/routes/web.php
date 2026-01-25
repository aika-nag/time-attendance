<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\BreakTimeController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
//一般ユーザー用ルート
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/', [AttendanceController::class, 'index']);
    Route::post('/attendance/begin', [AttendanceController::class, 'store']);
    Route::post('/attendance/finish', [AttendanceController::class, 'endDay']);
    Route::post('/attendance/break-begin', [BreakTimeController::class, 'store']);
    Route::post('/attendance/break-finish', [BreakTimeController::class, 'endBreakTime']);
});

Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect('/');
})->middleware(['auth', 'signed'])->name('verification.verify');
Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('message', 'Verification link sent!');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

//管理者用ルート
Route::prefix('admin')->group(function () {
    Route::get('/login', [AdminController::class, 'create'])->name('admin.login');
    Route::post('/login', [AdminController::class, 'store']);
    Route::middleware('auth:admin')->group(function () {
        Route::get('/attendance/list', [AdminController::class, 'index']);
        Route::post('/logout', [AdminController::class, 'destroy'])->name('admin.logout');
    });
});
