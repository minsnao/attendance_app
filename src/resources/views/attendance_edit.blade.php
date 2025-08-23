@extends('layouts.app')

@section('content')
<form id="attendance-request-form" action="{{ url('/attendance/request-update/' . ($attendance->id ?? 0)) }}" method="POST" data-attendance-id="{{ $attendance->id ?? 0 }}">
    @csrf
    <h2>勤怠詳細</h2>
    <p>名前: {{ $attendance->user->name }}</p>
    <p>日付: {{ $attendance->start_time ? $attendance->start_time->format('Y年m月d日') : ($attendance->date ?? '') }}</p>

    <label>出勤時間:
       <input type="time" name="requested_start_time"
       value="{{ $existingRequest ? \Carbon\Carbon::parse($existingRequest->requested_start_time)->format('H:i') : ($attendance->start_time ? $attendance->start_time->format('H:i') : '') }}"
       {{ $existingRequest ? 'readonly' : '' }}>
    </label>
    <br>

    <label>退勤時間:
        <input type="time" name="requested_end_time"
       value="{{ $existingRequest ? \Carbon\Carbon::parse($existingRequest->requested_end_time)->format('H:i') : ($attendance->end_time ? $attendance->end_time->format('H:i') : '') }}"
       {{ $existingRequest ? 'readonly' : '' }}>
    </label>
    <br>
    
    <h4>休憩時間</h4>
    <div id="break-request-list">
        @php
        if ($existingRequest && $existingRequest->requested_breaks) {
            $breaks = $existingRequest->requested_breaks;
            if (!is_array($breaks) || empty($breaks)) {
                $breaks = [['start_time'=>'','end_time'=>'']];
            }
        } else {
            $breaks = $attendance->breakTimes->isNotEmpty() ? $attendance->breakTimes->map(function($b) {
                return [
                    'start_time' => $b->start_time ? $b->start_time->format('H:i') : '',
                    'end_time' => $b->end_time ? $b->end_time->format('H:i') : ''
                ];
            })->toArray() : [['start_time'=>'','end_time'=>'']];
        }
        @endphp

        @foreach($breaks as $index => $break)
            <div class="break-item" data-index="{{ $index }}">
                休憩{{ $index + 1 }}:
                <input type="time"
                    name="breaks[{{ $index }}][start_time]"
                    value="{{ $break['start_time'] ?? '' }}"
                    {{ $existingRequest ? 'readonly' : '' }}>
                ～
                <input type="time"
                    name="breaks[{{ $index }}][end_time]"
                    value="{{ $break['end_time'] ?? '' }}"
                    {{ $existingRequest ? 'readonly' : '' }}>
                @if(!$existingRequest)
                    <button type="button" class="delete-break-btn">削除</button>
                @endif
            </div>
        @endforeach
    </div>
    @if(!$existingRequest)
    <button type="button" id="add-break-btn">休憩追加</button><br>
    <button type="submit">修正申請を送信</button>
    @endif
</form>

<a href="{{ url('/attendance/list') }}">一覧に戻る</a>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const breakList = document.getElementById('break-request-list');
        let breakIndex = breakList.querySelectorAll('.break-item').length;
        const addBreakBtn = document.getElementById('add-break-btn');
        if (addBreakBtn) {
            addBreakBtn.addEventListener('click', () => {
                const div = document.createElement('div');
                div.classList.add('break-item');
                div.dataset.index = breakIndex;
                div.innerHTML = `
                    休憩${breakIndex + 1}:
                    <input type="time" name="breaks[${breakIndex}][start_time]">
                    ～ 
                    <input type="time" name="breaks[${breakIndex}][end_time]">
                    <button type="button" class="delete-break-btn">削除</button>
                `;
                breakList.appendChild(div);
                breakIndex++;
            });
        }
        breakList.addEventListener('click', function(e){
            if(e.target.classList.contains('delete-break-btn')){
                e.target.closest('.break-item').remove();
            }
        });
    });
</script>
@endsection