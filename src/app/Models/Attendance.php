<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'date',
        'start_time',
        'end_time',
        'break_minutes',
    ];

    protected $casts = [
        'date' => 'date',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'break_minutes' => 'integer'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function correction()
    {
        return $this->hasMany(Correction::class);
    }

    public function getTotalWorkMinutesAttribute()
    {
        if($this->start_time && $this->end_time){
        $minutes = $this->end_time->diffInMinutes($this->start_time);
        $minutes -= $this->break_minutes ?? 0;

        return max($minutes, 0);
        }
        return null;
    }

    public function getTotalWorkTimeAttribute()
    {
        if($this->total_work_minutes === null){
            return null;
        }

        return $this->formatMinutes($this->total_work_minutes);
    }

    private function formatMinutes(int $minutes):string
    {
        $hours = intdiv($minutes, 60);
        $mins = $minutes % 60;

        return sprintf('%1d:%02d', $hours, $mins);//時間1桁、分2桁先頭０埋め
    }

    public function getBreakTimeAttribute()
    {
        if($this->break_minutes === null){
            return null;
        }

        return $this->formatMinutes($this->break_minutes);
    }
}
