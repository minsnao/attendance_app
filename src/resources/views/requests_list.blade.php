@extends('layouts.app')

@section('content')
<!-- 一般 -->

<h1>勤怠申請一覧</h1>

<div class="tabs">
    <button class="tab-btn active" data-status="requested">承認待ち</button>
    <button class="tab-btn" data-status="approved">承認済み</button>
</div>

<table>
    <tr>
        <th>状態</th>
        <th>対象日時</th>
        <th>申請理由</th>
        <th>申請日時</th>
        <th>詳細</th>
    </tr>

    @foreach($requests as $req)
    <tr class="request-item" data-status="{{ $req->status ?? 'requested' }}" 
        style="display: {{ ($req->status ?? 'requested') === 'requested' ? '' : 'none' }}">
        <td>{{ $req->status ?? '承認待ち' }}</td>
        <td>{{ $req->attendance->start_time->format('Y-m-d') }}</td>
        <td>{{ $req->remarks ?? '-' }}</td>
        <td>{{ $req->created_at->format('m月d日') }}</td>
        <td>
            <a href="/attendance/detail/{{ $req->attendance_id }}?date={{ $req->created_at->format('Y-m-d') }}">詳細</a>
        </td>
    </tr>
    @endforeach

    <tr class="empty-row" style="display: none;">
        <td colspan="5">申請はありません</td>
    </tr>
</table>

@endsection

@section('scripts')
<script>
const tabs = document.querySelectorAll('.tab-btn');
const rows = document.querySelectorAll('.request-item');
const emptyRow = document.querySelector('.empty-row');

function updateDisplay(status) {
    let hasVisible = false;
    rows.forEach(row => {
        if (row.dataset.status === status) {
            row.style.display = '';
            hasVisible = true;
        } else {
            row.style.display = 'none';
        }
    });
    if (emptyRow) emptyRow.style.display = hasVisible ? 'none' : '';
}

// 初期表示: 承認待ち
updateDisplay('requested');

tabs.forEach(tab => {
    tab.addEventListener('click', () => {
        tabs.forEach(t => t.classList.remove('active'));
        tab.classList.add('active');
        updateDisplay(tab.dataset.status);
    });
});
</script>
@endsection