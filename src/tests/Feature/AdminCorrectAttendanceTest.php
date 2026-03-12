<?php

namespace Tests\Feature;

use Database\Seeders\DatabaseSeeder;
use App\Models\User;
use App\Models\Admin;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Carbon\Carbon;

class AdminCorrectAttendanceTest extends TestCase
{
    use DatabaseMigrations;

    protected $users;

    protected function setUp(): void
    {
        parent::setUp();
        Carbon::setTestNow(today()->setHour(9)->setMinute(0)->setSecond(0));
        $this->seed(DatabaseSeeder::class);
        $this->users = User::orderBy('id')->get();
        $admin = Admin::first();
        $attendance = $this->users[1]->attendances()->where('date', today())->first();
        $this->actingAs($admin, 'admin')->get(route('admin.detail',[
            'attendance' => $attendance->id
        ]));
    }

    public function test_correct_validate_start_admin()
    {
        $response = $this->post('/admin/attendance/correction_requested', [
            'start_time' => '18:30',
            'end_time' => '18:00'
        ]);
        $response->assertStatus(302);
        $response->assertSessionHasErrors([
            'end_time' => '出勤時間もしくは退勤時間が不適切な値です'
            ]);
    }

    public function test_correct_validate_break_start_admin()
    {
        $response = $this->post('/admin/attendance/correction_requested', [
            'start_time' => '9:00',
            'end_time' => '18:00',
            'break_start_time' => [
                '18:30',
                '15:00'
            ],
            'break_end_time' => [
                '17:00',
                '15:30'
            ]
        ]);
        $response->assertStatus(302);
        $response->assertSessionHasErrors([
            'break_start_time.0' => '休憩時間が不適切な値です'
        ]);
    }

    public function test_correct_validate_break_end_admin()
    {
        $response = $this->post('/admin/attendance/correction_requested', [
            'start_time' => '9:00',
            'end_time' => '18:00',
            'break_start_time' => [
                '17:30'
            ],
            'break_end_time' => [
                '18:30',
            ]
        ]);
        $response->assertStatus(302);
        $response->assertSessionHasErrors([
            'break_end_time.0' => '休憩時間もしくは退勤時間が不適切な値です'
        ]);
    }

    public function test_correct_validate_reason_admin()
    {
        $response = $this->post('/admin/attendance/correction_requested', [
            'start_time' => '9:00',
            'end_time' => '18:00',
            'break_start_time' => [
                '12:30'
            ],
            'break_end_time' => [
                '13:30',
            ],
            'reason' => ''
        ]);
        $response->assertStatus(302);
        $response->assertSessionHasErrors([
            'reason' => '備考を記入してください'
        ]);
    }
}
