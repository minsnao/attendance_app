<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceRequest extends Model
{
    use HasFactory;
    protected $fillable = [
        'attendance_id',
        'user_id',
        'requested_start_time',
        'requested_end_time',
        'requested_breaks',
        'status',
        //'remarks',
    ];

    protected $casts = [
        'requested_breaks' => 'array',
        'requested_start_time' => 'datetime',
        'requested_end_time' => 'datetime',
    ];

    public function attendance()
    {
        return $this->belongsTo(Attendance::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
