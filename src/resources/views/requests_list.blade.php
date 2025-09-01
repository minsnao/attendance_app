@extends('layouts.app')

@section('content')

<h1>申請リスト</h1>

<table>
    <thead>
        <tr>
            <th>状態</th>
            <th>名前</th>
            <th>リクエスト日</th>
            <th>申請理由</th>
            <th>詳細</th>
        </tr>
    </thead>
    <tbody>
        @forelse($requests as $req)
            <tr>
                <td>{{ $req->status ?? '承認待ち' }}</td>
                <td>{{ $req->user->name }}</td>
                <td>{{ $req->created_at->format('m月d日') }}</td>
                <td>{{ $req->reason ?? '－' }}</td>
                <td>
                    <a href="/attendance/detail/{{ $req->attendance_id }}?date={{ $req->created_at->format('Y-m-d') }}">詳細</a>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="5">申請はありません</td>
            </tr>
        @endforelse
    </tbody>
</table>

@endsection


