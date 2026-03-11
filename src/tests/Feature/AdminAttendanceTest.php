<?php

namespace Tests\Feature;

use Database\Seeders\DatabaseSeeder;
use App\Models\User;
use App\Models\Admin;
use App\Models\Attendance;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Carbon\Carbon;

class AdminAttendanceTest extends TestCase
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
        $this->actingAs($admin, 'admin');
    }

    public function test_admin_all_attendance()
    {
        $response = $this->get('/admin/attendance/list');
        $response->assertStatus(200);
        $response->assertSeeInOrder([
            $this->users[1]->name,
            '09:00',
            $this->users[2]->name,
            '09:00',
            $this->users[3]->name,
            '09:00',
            '18:00',
            '1:00',
            '8:00'
        ]);
    }

    public function test_current_date()
    {
        $date = today()->format('Y年n月j日');
        $response = $this->get('/admin/attendance/list');
        $response->assertSee($date.'の勤怠');
    }

    public function test_previous_date()
    {
        $date = today()->subDay();
        Attendance::create([
            'user_id' => $this->users[4]->id,
            'date' => $date,
            'start_time' => '10:00:00',
            'end_time' => '15:00:00'
        ]);
        $response = $this->get(route('admin.index', ['date' => $date->format('Y-m-d')]));
        $response->assertSee($date->format('Y年n月j日').'の勤怠');
        $response->assertSeeInOrder([
            $this->users[4]->name,
            '10:00',
            '15:00',
        ]);
    }

    public function test_next_date()
    {
        $date = today()->addDay();
        Attendance::create([
            'user_id' => $this->users[5]->id,
            'date' => $date,
            'start_time' => '13:00:00',
            'end_time' => '19:00:00'
        ]);
        $response = $this->get(route('admin.index', ['date' => $date->format('Y-m-d')]));
        $response->assertSee($date->format('Y年n月j日').'の勤怠');
        $response->assertSeeInOrder([
            $this->users[5]->name,
            '13:00',
            '19:00',
        ]);
    }
}
