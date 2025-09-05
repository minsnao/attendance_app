@extends('layouts.app')

@section('content')

@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<h1>勤怠編集（管理者用）</h1>

<p>名前: {{ $attendance->user->name }}</p>
<p>日付: {{ $attendance->start_time->format('Y-m-d') }}</p>

<form action="{{ url('/admin/attendances/' . $attendance->id) }}" method="POST">
    @csrf
    @method('PUT')

    <p>
        出勤時間:
        <input type="time" name="start_time" value="{{ $attendance->start_time?->format('H:i') }}">
    </p>

    <p>
        退勤時間:
        <input type="time" name="end_time" value="{{ $attendance->end_time?->format('H:i') }}">
    </p>

    <h4>休憩時間</h4>
    <div id="break-list">
        @php
            $breaks = $attendance->breakTimes->isNotEmpty()
                ? $attendance->breakTimes->map(function($b) {
                    return [
                        'start_time' => $b->start_time ? $b->start_time->format('H:i') : '',
                        'end_time'   => $b->end_time ? $b->end_time->format('H:i') : ''
                    ];
                })->toArray()
                : [];

            $breakCount = max(2, count($breaks));
        @endphp

        @for ($i = 0; $i < $breakCount; $i++)
            @php
                $break = $breaks[$i] ?? ['start_time'=>'','end_time'=>''];
            @endphp
            <div class="break-item" data-index="{{ $i }}">
                休憩{{ $i + 1 }}:
                <input type="time" name="breaks[{{ $i }}][start_time]" value="{{ $break['start_time'] }}">
                ～
                <input type="time" name="breaks[{{ $i }}][end_time]" value="{{ $break['end_time'] }}">
                <button type="button" class="delete-break-btn">削除</button>
            </div>
        @endfor
    </div>

    <button type="button" id="add-break-btn">休憩追加</button>

    <p>
        備考:
        <textarea name="remarks">{{ $attendance->remarks }}</textarea>
    </p>

    <button type="submit">保存</button>
</form>

@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const breakList = document.getElementById('break-list');
        const addBtn = document.getElementById('add-break-btn');

        addBtn.addEventListener('click', () => {
            const index = breakList.querySelectorAll('.break-item').length;
            const div = document.createElement('div');
            div.className = 'break-item';
            div.dataset.index = index;
            div.innerHTML = `
                休憩${index + 1}:
                <input type="time" name="breaks[${index}][start_time]">
                ～
                <input type="time" name="breaks[${index}][end_time]">
                <button type="button" class="delete-break-btn">削除</button>
            `;
            breakList.appendChild(div);
        });

        breakList.addEventListener('click', (e) => {
            if (!e.target.classList.contains('delete-break-btn')) return;

            const item = e.target.closest('.break-item');
            item.remove();

            breakList.querySelectorAll('.break-item').forEach((el, i) => {
                el.dataset.index = i;
                const inputs = el.querySelectorAll('input');
                inputs[0].name = `breaks[${i}][start_time]`;
                inputs[1].name = `breaks[${i}][end_time]`;
                el.firstChild.textContent = `休憩${i + 1}:`;
            });
        });
    });
</script>
@endsection