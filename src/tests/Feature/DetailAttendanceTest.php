<?php

namespace Tests\Feature;

use Database\Seeders\DatabaseSeeder;
use App\Models\User;
use App\Models\Admin;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Carbon\Carbon;

class DetailAttendanceTest extends TestCase
{
    use DatabaseMigrations;

    protected $users;

    protected function setUp(): void
    {
        parent::setUp();
        Carbon::setTestNow(today()->setHour(9)->setMinute(0)->setSecond(0));
        $this->seed(DatabaseSeeder::class);
        $this->users = User::orderBy('id')->get();
    }

    public function test_match_attendance_data()
    {
        $user = $this->users[3];
        $response = $this->actingAs($user)->get(route('detail', ['attendance' => $user->attendances()->where('date', today())->first()->id]));
        $response->assertSeeInOrder([
            $user->name,
            today()->format('Y年'),
            today()->format('n月j日'),
            '09:00',
            '18:00',
            '12:00',
            '12:30',
            '15:00',
            '15:30'
        ]);
    }

    public function test_match_attendance_admin()
    {
        $admin = Admin::first();
        $attendance = $this->users[1]->attendances()->where('date', today())->first();
        $response = $this->actingAs($admin, 'admin')->get(route('admin.detail',[
            'attendance' => $attendance->id
        ]));
        $response->assertSeeInOrder([
            $attendance->user->name,
            today()->format('Y年'),
            today()->format('n月j日'),
            '09:00'
        ]);
    }
}
