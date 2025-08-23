<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;

class StaffUserController extends Controller
{
    public function index() {
        $staffUsers = User::where('role', 'employee')->get();
        return view('admin.staffs_list', compact('staffUsers'));
    }

    public function show(User $user, Request $request)
    {
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
            'attendance' => $attendances->get($dateString),
            ];
        }

        $prevMonthDate = $startOfMonth->copy()->subMonth();
        $nextMonthDate = $startOfMonth->copy()->addMonth();

        return view('admin.staff_attendances_list', [
            'user' => $user,
            'days' => $days,
            'year' => $year,
            'month' => $month,
            'prevYear' => $prevMonthDate->year,
            'prevMonth' => $prevMonthDate->month,
            'nextYear' => $nextMonthDate->year,
            'nextMonth' => $nextMonthDate->month,
        ]);
    }
}
