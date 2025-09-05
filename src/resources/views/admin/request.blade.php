@extends('layouts.app')

@section('content')

<h2>勤怠修正 詳細</h2>

<p>名前: {{ $requests->user->name }}</p>
<p>日付: {{ $requests->attendance->start_time->format('Y-m-d') }}</p>
<p>
    出勤時間 : {{ $requests->requested_start_time ? \Carbon\Carbon::parse($requests->requested_start_time)->format('H:i') : '-' }}
</p>
<p>
    退勤時間 : {{ $requests->requested_end_time ? \Carbon\Carbon::parse($requests->requested_end_time)->format('H:i') : '-' }}
</p>
<p>
    @foreach($breaks as $index => $break)
        <div class="break-item" data-index="{{ $index }}">
            休憩{{ $index + 1 }}:
            <input type="time" name="breaks[{{ $index }}][start_time]" value="{{ $break['start_time'] ?? '' }}" {{ $readonly ? 'readonly' : '' }}>
            ～
            <input type="time" name="breaks[{{ $index }}][end_time]" value="{{ $break['end_time'] ?? '' }}" {{ $readonly ? 'readonly' : '' }}>
        </div>
    @endforeach
</p>
<p>
    備考 : {{ $requests->remarks ?? '未記入' }}
</p>
<form action="{{ url('/admin/requests/' . $requests->id . '/approve') }}" method="POST">
    @csrf
    <button type="submit" id="approve-btn" {{ $requests->status === 'approved' ? 'disabled' : '' }}>{{ $requests->status === 'approved' ? '承認済み' : '承認する' }}</button>
</form>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const approveBtn = document.getElementById('approve-btn');
    if (!approveBtn) return;

    approveBtn.addEventListener('click', (e) => {
        e.preventDefault();

        approveBtn.disabled = true; 

        const requestId = "{{ $requests->id }}";
        fetch(`/admin/requests/${requestId}/approve`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
            body: JSON.stringify({})
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'approved') {
                approveBtn.disabled = true;
                approveBtn.textContent = '承認済み';
            } else {
                alert('承認できませんでした');
            }
        })
        .catch(err => console.error(err));
    });
});
</script>
@endsection