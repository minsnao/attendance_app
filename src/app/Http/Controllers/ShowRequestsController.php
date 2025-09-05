<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AttendanceRequest;

class ShowRequestsController extends Controller
{
    public function index() {
        $requests = AttendanceRequest::with(['user', 'attendance'])->orderBy('created_at','desc')->get();

        return view('admin.requests_list', compact('requests'));
    }

    public function show($id) {
        $requests = AttendanceRequest::with(['user', 'attendance'])->find($id);
        if (!empty($requests->requested_breaks) && is_array($requests->requested_breaks)) {
            $breaks = $requests->requested_breaks;
        } elseif ($requests->attendance && $requests->attendance->breakTimes->isNotEmpty()) {
            $breaks = $requests->attendance->breakTimes->map(function ($b) {
                return [
                    'start_time' => $b->start_time ? $b->start_time->format('H:i') : '',
                    'end_time' => $b->end_time   ? $b->end_time->format('H:i') : '',
                ];
            })->toArray();
        } else {
            $breaks = [];
        }
        while (count($breaks) < 2) {
            $breaks[] = ['start_time' => '', 'end_time' => ''];
        }
        $readonly = true;
        return view('admin.request', compact('requests', 'breaks', 'readonly'));
    }

    public function approve(Request $request, $id){
        $attendanceRequest = AttendanceRequest::findOrFail($id);

        // 念のため変更済みに加える場合エラー
        if ($attendanceRequest->status === 'approved') {
            return response()->json(['message' => '既に承認済みです'], 400);
        }

        $attendanceRequest->update(['status' => 'approved']);
        $attendance = $attendanceRequest->attendance;

        // 念のためレコードのチェック
        if ($attendance) {
            $attendance->update([
                'start_time' => $attendanceRequest->requested_start_time,
                'end_time' => $attendanceRequest->requested_end_time,
                'remarks' => $attendanceRequest->remarks,
            ]);
        }

        $attendance->breakTimes()->delete();
        // 念のためレコードのチェック
        foreach ($attendanceRequest->requested_breaks as $b) {
            $attendance->breakTimes()->create([
                'start_time' => $b['start_time'] ?: null,
                'end_time' => $b['end_time'] ?: null,
            ]);
        }

        // ここまで通るかチェック用
        // return response()->json([
        //     'message' => '承認されました',
        //     'status' => 'approved'
        // ]);
    }
}
