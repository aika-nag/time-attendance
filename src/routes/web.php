<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\BreakTimeController;
use App\Http\Controllers\CorrectionController;
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
    Route::get('/attendance/list', [AttendanceController::class, 'show']);
    Route::get('/stamp_correction_request/list', [CorrectionController::class, 'show'])->name('correction');
    Route::get('/attendance/detail/{attendance?}', [AttendanceController::class, 'detail'])->name('detail');
    Route::post('/attendance/detail/correction_requested', [CorrectionController::class, 'store']);
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
        Route::get('/staff/list', [AdminController::class, 'showStaff']);
        Route::get('/attendance/staff/{user}',[AdminController::class, 'showAttendance'])->name('admin.attendance');
        Route::get('/stamp_correction_request/list', [CorrectionController::class, 'showByAdmin'])->name('admin.correction');
        Route::get('/stamp_correction_request/approve/{correction?}', [CorrectionController::class, 'showCorrectionDetail'])->name('admin.request');
        Route::post('/stamp_correction_request/approve/{correction}', [AdminController::class, 'approve'])->name('admin.approve');
        Route::get('/attendance/{attendance?}', [AdminController::class, 'detail'])->name('admin.detail');
        Route::post('/attendance/correction_requested', [CorrectionController::class, 'storeByAdmin']);
        Route::get('/csv/export', [AdminController::class, 'exportCSV']);
    });
});
