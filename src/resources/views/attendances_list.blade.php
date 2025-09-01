@extends('layouts.app')

@section('content')

<!-- 一般従業員 勤怠リスト -->

<h1>勤怠一覧</h1>

<div>
    <a href="?year={{ $year }}&month={{ $month - 1 }}">◀ 前月</a>
    {{ $year }}年{{ $month }}月
    <a href="?year={{ $year }}&month={{ $month + 1 }}">翌月 ▶</a>
</div>

<table>
    <tr>
        <th>日付</th>
        <th>出勤時間</th>
        <th>退勤時間</th>
        <th>休憩時間</th>
        <th>合計勤務時間</th>
        <th>詳細</th>
    </tr>

    @foreach ($days as $day)
    @php
    $attendance = $day['attendance'];
    $date = $day['date'];
    @endphp
    <tr>
        <td>
            {{ $day['date'] }}
        </td>

        <td>
            {{ $attendance && $attendance->start_time ? \Carbon\Carbon::parse($attendance->start_time)->format('H:i') : '' }}
        </td>

        <td>
            {{ $attendance && $attendance->end_time ? \Carbon\Carbon::parse($attendance->end_time)->format('H:i') : '' }}
        </td>

        <td>
            @php
            $totalBreakMinutes = $attendance ? $attendance->breakTimes->sum(function ($break) {
                return $break->start_time && $break->end_time ? \Carbon\Carbon::parse($break->end_time)->diffInMinutes($break->start_time) : 0;
                }) : 0;
            echo $totalBreakMinutes > 0 ? floor($totalBreakMinutes / 60) . '時間' . ($totalBreakMinutes % 60) . '分' : '';
            @endphp
        </td>

        <td>
            @php
            if ($attendance && $attendance->start_time && $attendance->end_time) {
                $totalMinutes = \Carbon\Carbon::parse($attendance->end_time)->diffInMinutes($attendance->start_time) - $totalBreakMinutes;
                echo floor($totalMinutes / 60) . '時間' . ($totalMinutes % 60) . '分';
            }
            @endphp
        </td>
        <td>
            <a href="/attendance/detail{{ $attendance ? '/' . $attendance->id : '' }}?date={{ $date }}">詳細</a>
        </td>
    </tr>
    @endforeach
</table>

@endsection