<?php

namespace Tests\Feature;

use Database\Seeders\DatabaseSeeder;
use App\Models\User;
use App\Models\Admin;
use App\Models\Correction;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Carbon\Carbon;

class AdminCorrectionTest extends TestCase
{
    use DatabaseMigrations;

    protected $users;

    protected function setUp(): void
    {
        parent::setUp();
        Carbon::setTestNow(today()->setHour(19)->setMinute(0)->setSecond(0));
        $this->seed(DatabaseSeeder::class);
        $this->users = User::orderBy('id')->get();
        $admin = Admin::first();
        $this->actingAs($admin, 'admin');

        Correction::create([
            'user_id' => 2,
            'attendance_id' => $this->users[1]->attendances()->where('date', today())->first()->id,
            'date' => today(),
            'start_time' => '09:00',
            'end_time' => '18:00',
            'reason' => '打刻忘れのため',
            'status' => 1
        ]);
        Correction::create([
            'user_id' => 4,
            'attendance_id' => $this->users[3]->attendances()->where('date', today())->first()->id,
            'date' => today(),
            'start_time' => '09:00',
            'end_time' => '19:00',
            'reason' => '超勤が発生したため',
            'status' => 2
        ]);
        Correction::create([
            'user_id' => 5,
            'date' => today(),
            'start_time' => '09:00',
            'end_time' => '18:00',
            'reason' => '打刻忘れのため',
            'status' => 1
        ]);
    }

    public function test_admin_unapproved_approved_correction()
    {
        $response = $this->get(route('admin.correction'));
        $response->assertSeeInOrder([
            'class="unapproved-request"',
            '山田　太郎',
            '秋田　朋美',
            'class="approved-request"',
            '山本　敬吉'
        ], false);
    }

    public function test_admin_can_see_correction()
    {
        $response = $this->get(route('admin.request', 1));
        $response->assertSeeInOrder([
            '山田　太郎',
            today()->format('n月j日'),
            '09:00',
            '18:00',
            '打刻忘れのため',
            '承認'
        ], false);
    }

    public function test_admin_approve()
    {
        $response = $this->followingRedirects()->post(route('admin.approve', 1));
        $response->assertSee('承認済み');
        $this->assertDatabaseHas('corrections',[
            'user_id' => 2,
            'attendance_id' => $this->users[1]->attendances()->where('date', today())->first()->id,
            'date' => today(),
            'start_time' => '09:00:00',
            'end_time' => '18:00:00',
            'reason' => '打刻忘れのため',
            'status' => 2
        ]);
    }
}
