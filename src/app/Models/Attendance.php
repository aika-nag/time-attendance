<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'date',
        'start_time',
        'end_time',
    ];

    protected $casts = [
        'date' => 'date',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function breakTimes()
    {
        return $this->hasMany(BreakTime::class);
    }

    public function corrections()
    {
        return $this->hasMany(Correction::class);
    }

    public function getTotalBreakMinutesAttribute()
    {
        return $this->breakTimes->sum('break_minutes');
    }

    private function formatMinutes(int $minutes):string
    {
        $hours = intdiv($minutes, 60);
        $mins = $minutes % 60;

        return sprintf('%1d:%02d', $hours, $mins);//時間1桁、分2桁先頭０埋め
    }

    public function getTotalBreakTimeAttribute()
    {
        return $this->formatMinutes($this->total_break_minutes);
    }

    public function getTotalWorkMinutesAttribute()
    {
        if(!$this->start_time || !$this->end_time){
            return null;
        }
        $minutes = $this->end_time->diffInMinutes($this->start_time);
        $minutes -= $this->total_break_minutes;

        return $minutes;
    }

    public function getTotalWorkTimeAttribute()
    {
        if($this->total_work_minutes === null){
            return null;
        }

        return $this->formatMinutes($this->total_work_minutes);
    }
}
