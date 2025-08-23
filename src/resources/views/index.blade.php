@extends('layouts.app')

@section('content')

<h1>勤怠</h1>

<h2>{{ $user->name }}さんの勤怠ページ</h2>

<br><br><br>

<div>
    <div>
        <p id="status">状態：{{ $status }}</p>
    </div>

    <div>
        <p id="date">日付：--年--月--日</p>
    </div>

    <div>
        <p id="time">
            出勤時刻：
            @if(isset($attendance) && $attendance->start_time){{ \Carbon\Carbon::parse($attendance->start_time)->format('H:i:s') }}
            @else
            --:--:--
            @endif
        </p>
    </div>
    <button id="startBtn" class="hidden">出勤</button>
    <button id="break-btn" class="hidden">休憩</button>
    <button id="end-btn" class="hidden">退勤</button>
</div>
@endsection



@section('scripts')
<script>
function updateButtons(status) {
    const startBtn = document.getElementById('startBtn');
    const breakBtn = document.getElementById('break-btn');
    const endBtn = document.getElementById('end-btn');

    if (status === 'not_working') {
        startBtn.classList.remove('hidden');
        breakBtn.classList.add('hidden');
        endBtn.classList.add('hidden');
    } else if (status === 'working') {
        startBtn.classList.add('hidden');
        breakBtn.classList.remove('hidden');
        breakBtn.textContent = '休憩';
        endBtn.classList.remove('hidden');
    } else if (status === 'on_break') {
        startBtn.classList.add('hidden');
        breakBtn.classList.remove('hidden');
        breakBtn.textContent = '休憩終了';
        endBtn.classList.add('hidden');
    } else if (status === 'ended') {
        startBtn.classList.add('hidden');
        breakBtn.classList.add('hidden');
        endBtn.classList.add('hidden');
    }
}



const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

document.getElementById('startBtn').addEventListener('click', function () {
    console.log('出勤ボタン押されました');

    fetch('/attendance/start', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({})
    })
    .then(response => response.json())
    .then(data => {
        document.getElementById('status').textContent = '状態：' + data.status;
        document.getElementById('time').textContent = '出勤時刻：' + data.start_time;
        updateButtons(data.status);
    })
    .catch(error => {
        alert('エラーが発生しました');
        console.error(error);
    });
});

document.getElementById('break-btn').addEventListener('click', function () {
    const currentStatus = document.getElementById('status').textContent;

    if (currentStatus.includes('working')) {
        fetch('/attendance/break-start', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({})
        })
        .then(response => response.json())
        .then(data => {
            document.getElementById('status').textContent = '状態：' + data.status;
            document.getElementById('break-btn').textContent = '休憩終了';
            document.getElementById('end-btn').classList.add('hidden');
        });
    }

    else if (currentStatus.includes('on_break')) {
        fetch('/attendance/break-end', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({})
        })
        .then(response => response.json())
        .then(data => {
            document.getElementById('status').textContent = '状態：' + data.status;
            document.getElementById('break-btn').textContent = '休憩';
            document.getElementById('end-btn').classList.remove('hidden');
        });
    }
});

document.getElementById('end-btn').addEventListener('click', function () {
    fetch('/attendance/end', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({})
    })
    .then(response => response.json())
    .then(data => {
        document.getElementById('status').textContent = '状態：' + data.status;
        document.getElementById('end-btn').classList.add('hidden');
        document.getElementById('break-btn').classList.add('hidden');
    })
    .catch(error => {
        alert('退勤時にエラーが発生しました');
        console.error(error);
    });
});

window.addEventListener('DOMContentLoaded', function () {
    const now = new Date();
    const formattedDate = now.getFullYear() + '年' +
        String(now.getMonth() + 1).padStart(2, '0') + '月' +
        String(now.getDate()).padStart(2, '0') + '日';
    document.getElementById('date').textContent = '日付：' + formattedDate;

    const currentStatus = document.getElementById('status').textContent.replace('状態：', '').trim();

    updateButtons(currentStatus);
});
</script>
@endsection