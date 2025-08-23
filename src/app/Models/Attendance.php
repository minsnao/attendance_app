<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\BreakTime;
use App\Models\AttendanceRequest;


class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 
        'start_time', 
        'end_time',
        'status',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function breakTimes() {
        return $this->hasMany(BreakTime::class);
    }
    
    public function requests() {
        return $this->hasMany(AttendanceRequest::class);
    }

    public function getTotalBreakMinutesAttribute()
    {
        return $this->breaks->sum(function ($break){
            if ($break->start_time && $break->end_time) {
                return $break->end_time->diffInMinutes($break->start_time);
            }
            return 0;
        });
    }

    public function getTotalBreakHoursAttribute()
    {
        return round($this->total_break_minutes / 60, 2);
    }
}
