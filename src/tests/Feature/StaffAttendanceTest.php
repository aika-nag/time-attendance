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

class StaffAttendanceTest extends TestCase
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

    public function test_staff_information()
    {
        $response = $this->get('/admin/staff/list');
        $response->assertSeeInOrder([
            '西　怜奈',
            'reina.n@coachtech.com',
            '山田　太郎',
            'taro.y@coachtech.com',
            '増田　一世',
            'issei.m@coachtech.com',
            '山本　敬吉',
            'keikichi.y@coachtech.com',
            '秋田　朋美',
            'tomomi.a@coachtech.com',
            '中西　教夫',
            'norio.n@coachtech.com'
        ]);
    }

    public function test_staff_current_attendance()
    {
        $date = today();
        $response = $this->get('/admin/attendance/staff/2');
        $response->assertSee('山田　太郎さんの勤怠');
        $response->assertSee($date->format('Y/m'));
        $response->assertSeeInOrder([
            $date->format('m/d'),
            $date->isoFormat('ddd'),
            '09:00'
        ]);
    }

    public function test_staff_previous_month_attendance()
    {
        $date = today()->subMonth();
        $response = $this->get(route('admin.attendance', [
            'user' => '2',
            'date' => $date->format('Y-m')
        ]));
        $previousWorkCount = Attendance::where('user_id', 2)->whereYear('date', $date->year)->whereMonth('date', $date->month)->count();
        $countRow = substr_count($response->getContent(), '1:00');
        $response->assertSee($date->format('Y/m'));
        $this->assertEquals($previousWorkCount, $countRow);
    }

    public function test_staff_next_month_attendance()
    {
        $date = today()->addMonth();
        $attendances = Attendance::where('user_id', 2)->whereYear('date', $date->year)->whereMonth('date', $date->month)->get();
        $response = $this->get(route('admin.attendance', [
            'user' => '2',
            'date' => $date->format('Y-m')
        ]));
        $response->assertSee($date->format('Y/m'));
        $this->assertEquals(0, $attendances->count());

    }

    public function test_detail_link_admin()
    {
        $attendance = Attendance::where('user_id', 2)->where('date', today())->first();
        $response = $this->get('/admin/attendance/staff/2');
        $response->assertSee(route('admin.detail',[
            'attendance' => $attendance->id
        ]));

        $response2 = $this->get(route('admin.detail',[
            'attendance' => $attendance->id
        ]));
        $response2->assertStatus(200);
    }
}
