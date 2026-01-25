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
        'break_time',
        'status',
    ];

    protected $casts = [
        'start_time' => 'datetime: Y-m-d',
        'end_time' => 'datetime: Y-m-d'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
