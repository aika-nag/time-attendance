<?php

namespace Tests\Feature;

use Database\Seeders\DatabaseSeeder;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Carbon\Carbon;

class CorrectAttendanceTest extends TestCase
{
    use DatabaseMigrations;

    protected $users;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
        $this->users = User::orderBy('id')->get();

        Carbon::setTestNow(today()->setHour(9)->setMinute(0)->setSecond(0));
    }

    public function test_correct_validate_start_time()
    {
        $user = $this->users[3];
        $response = $this->actingAs($user)->post('/attendance/detail/correction_requested', [
            'start_time' => '18:30',
            'end_time' => '18:00'
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors([
            'end_time' => '出勤時間もしくは退勤時間が不適切な値です'
            ]);
    }

    public function test_correct_validate_break_start_time()
    {
        $user = $this->users[3];
        $response = $this->actingAs($user)->post('/attendance/detail/correction_requested', [
            'start_time' => '9:00',
            'end_time' => '18:00',
            'break_start_time' => [
                '18:30',
                '15:00'
            ],
            'break_end_time' => [
                '19:00',
                '15:30'
            ]
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors([
            'break_start_time.0' => '休憩時間が不適切な値です'
        ]);
    }

    public function test_correct_validate_break_end_time()
    {
        $user = $this->users[3];
        $response = $this->actingAs($user)->post('/attendance/detail/correction_requested', [
            'start_time' => '9:00',
            'end_time' => '18:00',
            'break_start_time' => [
                '12:00',
                '15:00'
            ],
            'break_end_time' => [
                '12:30',
                '18:30'
            ]
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors([
            'break_end_time.1' => '休憩時間もしくは退勤時間が不適切な値です'
        ]);
    }

    public function test_correct_validate_reason()
    {
        $user = $this->users[3];
        $response = $this->actingAs($user)->post('/attendance/detail/correction_requested', [
            'start_time' => '9:00',
            'end_time' => '18:00',
            'break_start_time' => ['12:00'],
            'break_end_time' => ['13:00'],
            'reason' =>''
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors([
            'reason' => '備考を記入してください'
        ]);
    }

    public function test_correct_request()
    {
        $user = $this->users[3];
        $response = $this->actingAs($user)->post('/attendance/detail/correction_requested', [
            'date' => today(),
            'start_time' => '9:00',
            'end_time' => '18:00',
            'break_start_time' => ['12:00'],
            'break_end_time' => ['13:00'],
            'reason' =>'休憩時間を変更しました'
        ]);

        $response->assertRedirect(route('detail', ['attendance' => $user->attendances()->where('date', today())->first()->id]));
        $this->assertDatabaseHas('corrections',[
            'user_id' => $user->id,
            'attendance_id' => $user->attendances()->where('date', today())->first()->id,
            'status' => 1
        ]);
    }
}
