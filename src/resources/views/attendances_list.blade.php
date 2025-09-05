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
                    $totalBreakMinutes = 0;
                    if ($attendance && $attendance->breakTimes->isNotEmpty()) {
                        $totalBreakMinutes = $attendance->breakTimes->sum(function ($break) {
                            return $break->start_time && $break->end_time ? \Carbon\Carbon::parse($break->end_time)->diffInMinutes($break->start_time) : 0;
                        });
                    }
                @endphp
                @if($attendance && $attendance->breakTimes->isNotEmpty())
                    @php
                        $totalBreakMinutes = $attendance ? $attendance->breakTimes->sum(function ($break) {
                            return $break->start_time && $break->end_time ? \Carbon\Carbon::parse($break->end_time)->diffInMinutes($break->start_time) : 0;
                        }) : 0;
                        $hours = floor($totalBreakMinutes / 60);
                        $minutes = $totalBreakMinutes % 60;
                        echo sprintf('%d:%02d', $hours, $minutes);
                    @endphp
                @elseif($attendance)
                    - {{-- 出勤あり∧休憩なし --}}
                @else
                    {{-- 未出勤時表示無し --}}
                @endif
            </td>

            <td>
                
                @php
                    if ($attendance && $attendance->start_time && $attendance->end_time) {
                        $totalMinutes = \Carbon\Carbon::parse($attendance->end_time)->diffInMinutes($attendance->start_time) - $totalBreakMinutes;
                        $hours = floor($totalMinutes / 60);
                        $minutes = $totalMinutes % 60;
                        echo sprintf('%d:%02d', $hours, $minutes);
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