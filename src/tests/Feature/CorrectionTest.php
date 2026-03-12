<?php

namespace Tests\Feature;

use Database\Seeders\DatabaseSeeder;
use App\Models\User;
use App\Models\Admin;
use App\Models\Correction;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Carbon\Carbon;

class CorrectionTest extends TestCase
{
    use DatabaseMigrations;

    protected $users;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
        $this->users = User::orderBy('id')->get();

        Carbon::setTestNow(today()->setHour(19)->setMinute(0)->setSecond(0));
        $this->actingAs($this->users[3])->post('/attendance/detail/correction_requested', [
            'date' => today(),
            'start_time' => '9:00',
            'end_time' => '19:00',
            'break_start_time' => ['12:00', '15:00'],
            'break_end_time' => ['12:30', '15:30'],
            'reason' =>'超勤が発生したため退勤時間を延長'
        ]);
    }

    public function test_admin_can_see_approve()
    {
        $admin = Admin::first();
        $correction = Correction::first();
        $response = $this->actingAs($admin, 'admin')->get(route('admin.request',[
            'correction' => $correction->id
        ]));
        $response->assertSee('承認');
        $response->assertSee($this->users[3]->name);
        $response->assertSee('超勤が発生したため退勤時間を延長');
    }

    public function test_admin_can_see_correction_list()
    {
        $admin = Admin::first();
        $correction = Correction::first();
        $response = $this->actingAs($admin, 'admin')->get(route('admin.correction'));
        $response->assertSee('id="unapproved" checked', false);
        $response->assertSee($correction->user->name);
        $response->assertSee($correction->reason);
    }

    public function test_user_can_see_correction_list()
    {
        $user = $this->users[3];
        $correction = Correction::first();
        $response = $this->actingAs($user)->get(route('correction'));
        $response->assertSee('id="unapproved" checked', false);
        $content = $response->getContent();
        $this->assertEquals(1, substr_count($content, $correction->user->name));
        $response->assertSee($correction->reason);
    }

    public function test_user_can_see_approved_correction()
    {
        $user = $this->users[3];
        $correction = Correction::find(1);
        $correction->update([
            'status' => 2,
        ]);
        $response = $this->actingAs($user)->get(route('correction'));
        $response->assertSeeInOrder([
            'class="approved-request"',
            $correction->name,
            $correction->date->format('Y/m/d'),
            $correction->reason
        ], false);
    }

    public function test_user_can_see_correction_detail()
    {
        $response = $this->actingAs($this->users[3])->get(route('correction'));
        $correction = Correction::find(1);
        $response->assertSee(route('detail', $correction->attendance_id));
        $response->assertSee('詳細');
        $response2 = $this->actingAs($this->users[3])->get(route('detail', $correction->attendance_id));
        $response2->assertStatus(200);
    }
}
