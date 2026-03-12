<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class AttendancesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->seedLastMonth();
        $this->seedToday();
    }

    private function seedLastMonth()
    {
        $users = User::orderBy('id')->take(5)->get();
        $start = now()->subMonth()->startOfMonth();
        $end = now()->subMonth()->endOfMonth();

        foreach($users as $user){
            for($date = $start->copy(); $date<=$end; $date->addDay()){
                if($date->isWeekend()){
                    continue;
                }
                $attendance = $user->attendances()->create([
                    'date' => $date->format('Y-m-d'),
                    'start_time' => '09:00:00',
                    'end_time' => '18:00:00'
                ]);
                $attendance->breakTimes()->create([
                    'start_time' => '12:00:00',
                    'end_time' => '13:00:00'
                ]);
            }
        }
    }

    private function seedToday()
    {
        $users = User::orderBy('id')->take(4)->get();
        $today = today();
        //ユーザー１：出勤前、ユーザー２：出勤中
        $users[1]->attendances()->create([
            'date' => $today,
            'start_time' => '09:00:00',
            'end_time' => null
        ]);
        //ユーザー３：休憩中
        $attendance = $users[2]->attendances()->create([
            'date' =>$today,
            'start_time' => '09:00:00',
            'end_time' => null
        ]);
        $attendance->breakTimes()->create([
            'start_time' => '09:15:00',
            'end_time' => null
        ]);
        //ユーザー４：退勤済み、休憩２回
        $attendance = $users[3]->attendances()->create([
            'date' => $today,
            'start_time' => '09:00:00',
            'end_time' => '18:00:00'
        ]);
        $attendance->breakTimes()->create([
            'start_time' => '12:00:00',
            'end_time' => '12:30:00'
        ]);
        $attendance->breakTimes()->create([
            'start_time' => '15:00:00',
            'end_time' => '15:30:00'
        ]);
    }
}
