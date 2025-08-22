@extends('layouts.app')

@section('content')
<form id="attendance-request-form" action="{{ url('/attendance/request-update/' . ($attendance->id ?? 0)) }}" method="POST" data-attendance-id="{{ $attendance->id ?? 0 }}">
    @csrf
    <h2>勤怠詳細</h2>
    <p>名前: {{ $attendance->user->name }}</p>
    <p>日付: {{ $attendance->start_time ? $attendance->start_time->format('Y年m月d日') : ($attendance->date ?? '') }}</p>

    <label>出勤時間:
        <input type="time" name="requested_start_time" value="{{ $attendance->start_time ? $attendance->start_time->format('H:i') : '' }}">
    </label>
    <br>

    <label>退勤時間:
        <input type="time" name="requested_end_time" value="{{ $attendance->end_time ? $attendance->end_time->format('H:i') : '' }}">
    </label>
    <br>

    <h4>休憩時間</h4>
    <div id="break-request-list">
        @php
            $breaks = $attendance->breakTimes->isNotEmpty() ? $attendance->breakTimes : [null]; 
        @endphp

        @foreach($breaks as $index => $break)
        <div class="break-item" data-index="{{ $index }}">
            休憩{{ $index + 1 }}:
            <input type="time" name="breaks[{{ $index }}][start_time]" value="{{ $break ? ($break->start_time ? $break->start_time->format('H:i') : '') : '' }}">
            ～
            <input type="time" name="breaks[{{ $index }}][end_time]" value="{{ $break ? ($break->end_time ? $break->end_time->format('H:i') : '') : '' }}">
            <button type="button" class="delete-break-btn">削除</button>
        </div>
        @endforeach
    </div>

    <button type="button" id="add-break-btn">休憩追加</button><br>
    <button type="submit">修正申請を送信</button>
</form>

<a href="{{ url('/attendance/list') }}">一覧に戻る</a>

@endsection



@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const form = document.getElementById('attendance-request-form');
        const breakList = document.getElementById('break-request-list');
        const attendanceId = form.dataset.attendanceId || 0;
        let breakIndex = breakList.querySelectorAll('.break-item').length;

        console.log(form);
        console.log(breakList);

        document.getElementById('add-break-btn').addEventListener('click', () => {
            const div = document.createElement('div');
            div.classList.add('break-item');
            div.dataset.index = breakIndex;
            div.innerHTML = `
                休憩${breakIndex+1}:
                <input type="time" name="breaks[${breakIndex}][start_time]">
                ～
                <input type="time" name="breaks[${breakIndex}][end_time]">
                <button type="button" class="delete-break-btn">削除</button>
            `;
            breakList.appendChild(div);
            breakIndex++;
        });

        breakList.addEventListener('click', function(e){
            if(e.target.classList.contains('delete-break-btn')){
                e.target.closest('.break-item').remove();
            }
        });

        document.getElementById('attendance-request-form').addEventListener('submit', function(e){
            e.preventDefault();
            const formData = new FormData(this);
            const breaks = [];
            
            formData.forEach((value, key) => {
                const match = key.match(/breaks\[(\d+)\]\[(start_time|end_time)\]/);
                if(match){
                    const index = parseInt(match[1]);
                    const field = match[2];
                    if(!breaks[index]) breaks[index] = {};
                    breaks[index][field] = value;
                }
            });

            const payload = {
                requested_start_time: formData.get('requested_start_time') || null,
                requested_end_time: formData.get('requested_end_time') || null,
                requested_breaks: breaks.filter(b => b && (b.start_time || b.end_time)),
                _token: formData.get('_token')
            };

            fetch(`/attendance/request/${attendanceId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(payload)
            })
            .then(res => res.json())
            .then(data => {
                alert('修正申請を送信しました');
                console.log(data);
            })
            .catch(err => console.error(err));
        });
    });
</script>
@endsection