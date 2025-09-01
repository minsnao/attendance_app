@extends('layouts.app')

@section('content')

<!-- 管理者用 該当従業員 勤怠リスト -->

<h2>{{ $user->name }} さんの勤怠一覧</h2>

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
    @endphp
    <tr>
        <td>
            {{ $day['date'] }}
        </td>

        <td>
            {{ $attendance ? \Carbon\Carbon::parse($attendance->start_time)->format('H:i') : '' }}
        </td>

        <td>
            {{ $attendance && $attendance->end_time ? \Carbon\Carbon::parse($attendance->end_time)->format('H:i') : '' }}
        </td>

        <td>
            @if ($attendance)
            @php
            $totalBreakMinutes = $attendance->breakTimes->sum(function ($break) {
                return $break->start_time && $break->end_time ? \Carbon\Carbon::parse($break->end_time)->diffInMinutes($break->start_time) : 0;
            });
            echo $totalBreakMinutes > 0 ? floor($totalBreakMinutes / 60) . '時間' . ($totalBreakMinutes % 60) . '分' : '';
            @endphp
            @endif
        </td>

        <td>
            @if ($attendance && $attendance->start_time && $attendance->end_time)
            @php
            $totalMinutes = \Carbon\Carbon::parse($attendance->end_time)->diffInMinutes(\Carbon\Carbon::parse($attendance->start_time));
            $totalBreakMinutes = $attendance->breakTimes->sum(function ($break) {
                return $break->start_time && $break->end_time ? \Carbon\Carbon::parse($break->end_time)->diffInMinutes($break->start_time) : 0;
            });
            $totalMinutes -= $totalBreakMinutes;
            echo floor($totalMinutes / 60) . '時間' . ($totalMinutes % 60) . '分';
            @endphp
            @endif
        </td>
        <td>
            <a href="{{ url('/admin/attendances' . ($attendance ? '/' . $attendance->id : '') . '?date=' . $day['date']) }}">詳細</a>
        </td>
    </tr>
    @endforeach
</table>

@endsection