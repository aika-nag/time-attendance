<?php

namespace Tests\Feature;

use Database\Seeders\DatabaseSeeder;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
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
        $this->seed(DatabaseSeeder::class);
        $this->users = User::orderBy('id')->get();
    }

    public function test_match_attendance_data()
    {
        $user = $this->users[3];
        Carbon::setTestNow(today()->setHour(9)->setMinute(0)->setSecond(0));
        $response = $this->actingAs($user)->get(route('detail', ['attendance' => $user->attendances()->where('date', today())->first()->id]));

        $response->assertSee($user->name);
        $response->assertSee(today()->format('Y年'));
        $response->assertSee(today()->format('n月j日'));
        $response->assertSee(today()->format('09:00'));
        $response->assertSee(today()->format('18:00'));
        $response->assertSee(today()->format('12:00'));
        $response->assertSee(today()->format('12:30'));
        $response->assertSee(today()->format('15:00'));
        $response->assertSee(today()->format('15:30'));
    }
}
