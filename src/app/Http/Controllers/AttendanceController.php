<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Attendance;
use App\Models\BreakTime;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class AttendanceController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $today = Carbon::today();

        $attendance = Attendance::where('user_id', $user->id)->whereDate('start_time', $today)->latest()->first();

        $status = 'not_working';

        if ($attendance) {
            if ($attendance->end_time) {
                $status = 'ended';
            } else {
                $latestBreak = $attendance->breakTimes()->latest()->first();
                if ($latestBreak && $latestBreak->end_time === null) {
                    $status = 'on_break';
                } else {
                    $status = 'working';
                }
            }
        }

        return view('index', compact('user', 'status', 'attendance'));
    }


    public function start(Request $request)
    {
        $user = Auth::user();
        $today = now()->toDateString();

        $attendance = Attendance::where('user_id', $user->id)->whereDate('start_time', $today)->first();

        if (!$attendance) {
            $attendance = Attendance::create([
                'user_id' => $user->id,
                'start_time' => now(),
                'status' => 'working',
            ]);
        } else {
            $attendance->status = 'working';
            $attendance->start_time = $attendance->start_time ?? now();
            $attendance->save();
        }

        return response()->json([
            'status' => $attendance->status,
            'start_time' => $attendance->start_time->format('H:i:s'),
        ]);
    }

    
    public function breakStart(Request $request)
    {
        $user = Auth::user();

        $attendance = Attendance::where('user_id', $user->id)->whereDate('start_time', now()->toDateString())->latest()->first();

        if (!$attendance || $attendance->status !== 'working') {
            return response()->json(['error' => '勤務中ではありません'], 400);
        }

        $breakTime = $attendance->breakTimes()->create([
            'start_time' => now(),
        ]);

        $attendance->update(['status' => 'on_break']);

        return response()->json([
            'status' => 'on_break',
            'break_id' => $breakTime->id,
            'break_start' => $breakTime->start_time->format('H:i:s'),
        ]);
    }


    public function breakEnd(Request $request)
    {
        $user = Auth::user();

        $attendance = Attendance::where('user_id', $user->id)->whereDate('start_time', now()->toDateString())->latest()->first();

        if (!$attendance || $attendance->status !== 'on_break') {
            return response()->json(['error' => '休憩中ではありません'], 400);
        }

        $breakTime = $attendance->breakTimes()->latest()->first();
        if (!$breakTime || $breakTime->end_time) {
            return response()->json(['error' => '休憩開始が存在しません'], 400);
        }

        $breakTime->update(['end_time' => now()]);

        $attendance->update(['status' => 'working']);

        return response()->json([
            'status' => 'working',
            'break_end' => $breakTime->end_time->format('H:i:s'),
        ]);
    }

    public function end(Request $request)
    {
        $user = Auth::user();

        $attendance = Attendance::where('user_id', $user->id)->whereDate('created_at', now()->toDateString())->latest()->first();

        if (!$attendance || !in_array($attendance->status, ['working', 'on_break'])) {
            return response()->json(['error' => '勤務中または休憩中ではありません'], 400);
        }

        $attendance->update([
            'end_time' => now(),
            'status' => 'ended',
        ]);

        return response()->json([
            'status' => $attendance->status,
            'end_time' => $attendance->end_time->format('H:i:s'),
        ]);
    }



    public function show(Request $request)
    {
        $user = Auth::user();
        $year = $request->input('year', now()->year);
        $month = $request->input('month', now()->month);

        $attendances = Attendance::where('user_id', $user->id)->whereYear('start_time', $year)->whereMonth('start_time', $month)->get()->keyBy(function ($attendance) {
            return \Carbon\Carbon::parse($attendance->start_time)->toDateString();
        });

        $startOfMonth = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endOfMonth = $startOfMonth->copy()->endOfMonth();

        $days = [];
        for ($date = $startOfMonth->copy(); $date->lte($endOfMonth); $date->addDay()) {
        $dateString = $date->toDateString();
        $days[] = [
            'date' => $dateString,
            'attendance' => $attendances->get($dateString), // 出勤していない日は null
            ];
        }

        $prevMonthDate = $startOfMonth->copy()->subMonth();
        $nextMonthDate = $startOfMonth->copy()->addMonth();

        return view('attendances_list', [
            'days' => $days,
            'year' => $year,
            'month' => $month,
            'prevYear' => $prevMonthDate->year,
            'prevMonth' => $prevMonthDate->month,
            'nextYear' => $nextMonthDate->year,
            'nextMonth' => $nextMonthDate->month,
        ]);
    }

    public function edit(Request $request, $id = null)
    {
        $user = Auth::user();
        $date = $request->query('date');

        $attendance = $id ? Attendance::where('id', $id)->where('user_id', $user->id)->first() : Attendance::where('user_id', $user->id)->whereDate('start_time', $date)->first();

        if (!$attendance) {
            $attendance = new Attendance();
            $attendance->user_id = $user->id;
            $attendance->start_time = null;
            $attendance->end_time = null;
            $attendance->setRelation('breakTimes', collect());
            $attendance->date = $date;
        } else {
            $attendance->load('breakTimes');
        }

        return view('attendance_edit', compact('attendance'));
    }

    public function requestUpdate(Request $request, $id)
    {
        $user = Auth::user();

        $attendance = Attendance::where('id', $id)->where('user_id', $user->id)->firstOrFail();

        $requestedBreaks = $request->input('requested_breaks', []); 

        $existingRequest = $attendance->requests()->where('status', 'requested')->first();
        if (!$existingRequest) {
            $attendance->requests()->create([
                'user_id' => $user->id,
                'requested_start_time' => $request->input('requested_start_time'),
                'requested_end_time' => $request->input('requested_end_time'),
                'requested_breaks' => json_encode($requestedBreaks),
                'status' => 'requested',
            ]);
        }
        return back();
    }

    public function approveRequest($id)
    {
        $request = Attendance::with('attendance')->findOrFail($id);
        $attendance = $request->attendance;

        if ($request->requested_start_time) {
            $attendance->start_time = $request->requested_start_time;
        }
        if ($request->requested_end_time) {
            $attendance->end_time = $request->requested_end_time;
        }

        if ($request->requested_breaks) {
            $breaks = json_decode($request->requested_breaks, true);

            $attendance->breakTimes()->delete();

            foreach ($breaks as $b) {
                $attendance->breakTimes()->create([
                    'start_time' => $b['start_time'],
                    'end_time' => $b['end_time'],
                ]);
            }
        }

        $attendance->save();

        $request->status = 'approved';
        $request->save();

        return response()->json([
            'request_id' => $request->id,
            'status' => $request->status,
            'attendance' => [
                'start_time' => $attendance->start_time ? $attendance->start_time->format('H:i') : null,
                'end_time' => $attendance->end_time ? $attendance->end_time->format('H:i') : null,
                'breaks' => $attendance->breakTimes->map(function($b) {
                    return [
                        'start_time' => $b->start_time ? $b->start_time->format('H:i') : null,
                        'end_time' => $b->end_time ? $b->end_time->format('H:i') : null,
                    ];
                }),
            ]
        ]);
    }

    public function appry()
    {
        $requests = Attendance::with('attendance', 'user')->where('status', 'requested')->latest()->get();

        return view('requests_list', compact('requests'));
    }
}
