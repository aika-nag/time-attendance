<?php

namespace Tests\Feature;

use Database\Seeders\DatabaseSeeder;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Carbon\Carbon;

class AttendanceTest extends TestCase
{
    use DatabaseMigrations;

    protected $users;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
        $this->users = User::orderBy('id')->get();
    }

    public function test_date_match()
    {
        $today = now();
        $response = $this->actingAs($this->users[0])->get('/');
        $response->assertSee($today->format('Y年n月j日')."(".$today->isoformat('ddd').")");
        $response->assertSee('<p class="time" id="time"></p>', false);
    }

    public function test_status()
    {
        $response1 = $this->actingAs($this->users[0])->get('/');
        $response1->assertSee('勤務外');
        $response2 = $this->actingAs($this->users[1])->get('/');
        $response2->assertSee('出勤中');
        $response3 = $this->actingAs($this->users[2])->get('/');
        $response3->assertSee('休憩中');
        $response4 = $this->actingAs($this->users[3])->get('/');
        $response4->assertSee('退勤済');
    }

    public function test_start_work()
    {
        Carbon::setTestNow(today()->setHour(9)->setMinute(0)->setSecond(0));
        $response1 = $this->actingAs($this->users[0])->followingRedirects()->post('/attendance/begin');
        $response1->assertSee('出勤中');
        $response2 = $this->actingAs($this->users[3])->get('/');
        $response2->assertDontSee('/attendance/begin');
        $this->actingAs($this->users[4])->post('/attendance/begin');
        $response3 = $this->actingAs($this->users[4])->get('/attendance/list');
        $response3->assertSee('09:00');
    }

    public function test_start_break()
    {
        Carbon::setTestNow(today()->setHour(9)->setMinute(0)->setSecond(0));
        $response = $this->actingAs($this->users[1])->get('/');
        $response->assertSee('/attendance/break-begin');
        Carbon::setTestNow(today()->setHour(11)->setMinute(0)->setSecond(0));
        $response2 = $this->actingAs($this->users[1])->followingRedirects()->post('/attendance/break-begin');
        $response2->assertSee('休憩中');
        $response2->assertSee('/attendance/break-finish');
        Carbon::setTestNow(today()->setHour(12)->setMinute(0)->setSecond(0));
        $response3 = $this->actingAs($this->users[1])->followingRedirects()->post('/attendance/break-finish');
        $response3->assertSee('/attendance/break-begin');
        $response3->assertSee('出勤中');
        Carbon::setTestNow(today()->setHour(15)->setMinute(30)->setSecond(0));
        $response4 = $this->actingAs($this->users[1])->followingRedirects()->post('/attendance/break-begin');
        $response4->assertSee('/attendance/break-finish');
        Carbon::setTestNow(today()->setHour(16)->setMinute(00)->setSecond(0));
        $this->actingAs($this->users[1])->post('/attendance/break-finish');
        $response5 = $this->actingAs($this->users[1])->get('/attendance/list');
        $response5->assertSee('1:30');
        $attendance = $this->users[1]->attendances->whereNull('end_time')->first();
        $this->assertDatabaseHas('break_times',[
            'attendance_id' => $attendance->id,
            'start_time' => '11:00:00',
            'end_time' => '12:00:00'
        ]);
        $this->assertDatabaseHas('break_times',[
            'attendance_id' => $attendance->id,
            'start_time' => '15:30:00',
            'end_time' => '16:00:00'
        ]);
    }

    public function test_end_work()
    {
        $response = $this->actingAs($this->users[1])->get('/');
        $response->assertSee('/attendance/finish');
        $response2 = $this->actingAs($this->users[1])->followingRedirects()->post('/attendance/finish');
        $response2->assertSee('退勤済');

        Carbon::setTestNow(today()->setHour(9)->setMinute(00)->setSecond(0));
        $this->actingAs($this->users[0])->post('/attendance/begin');
        Carbon::setTestNow(today()->setHour(18)->setMinute(00)->setSecond(0));
        $this->actingAs($this->users[0])->post('/attendance/finish');
        $this->actingAs($this->users[0])->get('/attendance/list');
        $this->assertDatabaseHas('attendances',[
            'user_id' => $this->users[0]['id'],
            'date' => today()->format('Y-m-d'),
            'start_time' => '09:00:00',
            'end_time' => '18:00:00'
        ]);
    }

    public function test_current_attendance()
    {
        $user = $this->users[3];
        Carbon::setTestNow(today()->setHour(9)->setMinute(0)->setSecond(0));
        $start = today()->subMonth()->startOfMonth();
        $end = today()->subMonth()->endOfMonth();
        $workDays = $start->diffInWeekdays($end);

        $response = $this->actingAs($user)->get('/attendance/list');
        $response->assertSee(today()->format('Y/m'));
        $this->assertDatabaseHas('attendances',[
            'user_id' => $this->users[3]['id'],
            'date' => today()->format('Y-m-d'),
            'start_time' => '09:00:00',
            'end_time' => '18:00:00'
        ]);
        $this->assertEquals($workDays + 1, $user->attendances->count());
        $response2 = $this->actingAs($user)->get(route('detail', ['attendance' => $user->attendances->where('date', today())->first()->id]));
        $response2->assertStatus(200);
    }

    public function test_previous_and_next_attendances()
    {
        $user = $this->users[3];
        Carbon::setTestNow(today()->setHour(9)->setMinute(0)->setSecond(0));
        $start = today()->subMonth()->startOfMonth();
        $end = today()->subMonth()->endOfMonth();
        $workDays = $start->diffInWeekdays($end);
        $nextMonth = today()->addMonth();

        $response = $this->actingAs($user)->get(route('attendance', ['date' => $start->format('Y-m')]));
        $response->assertSee($start->format('Y/m'));
        $this->assertEquals($workDays, $user->attendances()->whereYear('date',$start->year)->whereMonth('date', $start->month)->count());
        $response2 = $this->actingAs($user)->get(route('attendance', ['date' => $nextMonth->format('Y-m')]));
        $response2->assertSee($nextMonth->format('Y/m'));
        $this->assertEquals(0, $user->attendances()->whereYear('date',$nextMonth->year)->whereMonth('date', $nextMonth->month)->count());
    }
}
