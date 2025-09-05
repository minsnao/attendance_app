<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Attendance;
use App\Models\BreakTime;
use Illuminate\Http\Request;

class AttendanceListController extends Controller
{
    public function index(Request $request) 
    {
        $year  = $request->query('year', Carbon::now()->year);
        $month = $request->query('month', Carbon::now()->month);
        $day   = $request->query('day', Carbon::now()->day);

        $date = Carbon::create($year, $month, $day);

        $attendances = Attendance::with('user', 'breakTimes')->whereDate('start_time', $date)->orderBy('start_time', 'desc')->get();

        return view('admin.attendances_list', compact('attendances', 'year', 'month', 'day', 'date'));
    }

    public function edit($id) 
    {
        $attendance = Attendance::with('breakTimes', 'user')->findOrFail($id);

        return view('admin.admin_attendance_edit', compact('attendance'));
    }

    public function update(Request $request, $id)
    {
        $attendance = Attendance::findOrFail($id);

        $attendance->start_time = $request->start_time;
        $attendance->end_time   = $request->end_time;
        $attendance->remarks    = $request->remarks;
        $attendance->save();

        $attendance->breakTimes()->delete();

        if ($request->has('breaks')) {
            foreach ($request->breaks as $breakData) {
                $start = $breakData['start_time'] ?? null;
                $end   = $breakData['end_time'] ?? null;

                BreakTime::create([
                    'attendance_id' => $attendance->id,
                    'start_time'    => $start,
                    'end_time'      => $end,
                ]);
            }
        }
        return redirect()->back();
    }
}