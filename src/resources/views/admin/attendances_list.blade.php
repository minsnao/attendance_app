@extends('layouts.app')

@section('content')

<!-- 管理者用 従業員勤怠リスト -->

<h1>勤怠一覧</h1>

<div>
    <a href="?year={{ $date->copy()->subDay()->year }}&month={{ $date->copy()->subDay()->month }}&day={{ $date->copy()->subDay()->day }}">◀ 前日</a>
    {{ $date->format('Y年m月d日') }}の勤怠
    <a href="?year={{ $date->copy()->addDay()->year }}&month={{ $date->copy()->addDay()->month }}&day={{ $date->copy()->addDay()->day }}">翌日 ▶</a>
</div>

<table>
    <thead>
        <tr>
            <th>名前</th>
            <th>出勤</th>
            <th>退勤</th>
            <th>休憩</th>
            <th>合計</th>
            <th>詳細</th>
        </tr>
    </thead>
    <tbody>
        @foreach($attendances as $attendance)
        @php
            $totalBreakMinutes = $attendance->breakTimes->sum(function($b) {
                return $b->start_time && $b->end_time 
                    ? \Carbon\Carbon::parse($b->end_time)->diffInMinutes(\Carbon\Carbon::parse($b->start_time))
                    : 0;
            });

            $workMinutes = $attendance->end_time 
                ? \Carbon\Carbon::parse($attendance->end_time)->diffInMinutes(\Carbon\Carbon::parse($attendance->start_time)) - $totalBreakMinutes
                : 0;

            $totalBreak = sprintf('%02d:%02d', intdiv($totalBreakMinutes, 60), $totalBreakMinutes % 60);
            $totalWork  = sprintf('%02d:%02d', intdiv($workMinutes, 60), $workMinutes % 60);
        @endphp
        <tr>
            <td>{{ $attendance->user->name }}</td>
            <td>{{ $attendance->start_time->format('H:i') }}</td>
            <td>{{ $attendance->end_time ? $attendance->end_time->format('H:i') : '-' }}</td>
            <td>{{ $totalBreak }}</td>
            <td>{{ $totalWork }}</td>
            <td><a href="{{ url('/admin/attendances/' . $attendance->id) }}">詳細</a></td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection