@extends('layouts.app')

@section('content')
<!-- 管理者 -->

<h1>勤怠申請一覧</h1>

<div class="tabs">
    <button class="tab-btn active" data-status="requested">承認待ち</button>
    <button class="tab-btn" data-status="approved">承認済み</button>
</div>

<table>
    <tr>
        <th>状態</th>
        <th>名前</th>
        <th>対象日時</th>
        <th>申請理由</th>
        <th>申請日時</th>
        <th>詳細</th>
    </tr>
    
    @forelse($requests as $req)
        <tr class="request-item" data-status="{{ $req->status ?? 'requested' }}" style="display: {{ ($req->status ?? 'requested') === 'requested' ? '' : 'none' }}">
            <td>{{ $req->status ?? '承認待ち' }}</td>
            <td>{{ $req->user->name }}</td>
            <td>{{ $req->attendance->start_time->format('Y-m-d') }}</td>
            <td>{{ $req->remarks ?? '-' }}</td>
            <td>{{ $req->created_at->format('m月d日') }}</td>
            <td>
                <a href="/admin/requests/{{ $req->attendance_id }}?date={{ $req->created_at->format('Y-m-d') }}">詳細</a>
            </td>
        </tr>
    @empty
        <tr>
            <td>申請はありません</td>
        </tr>
    @endforelse
</table>

@endsection

@section('scripts')
<script>    
    const tabs = document.querySelectorAll('.tab-btn');
    const rows = document.querySelectorAll('.request-item');

    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            tabs.forEach(t => t.classList.remove('active'));
            tab.classList.add('active');
            
            const status = tab.dataset.status;
            rows.forEach(row => {
                row.style.display = row.dataset.status === status ? '' : 'none';
            });
        });
    });
</script>
@endsection