@extends('layouts.app')

@section('content')

<h2>スタッフ一覧</h2>

<table>
    <thead>
        <tr>
            <th>名前</th>
            <th>メールアドレス</th>
            <th>詳細</th>
        </tr>
    </thead>
    <tbody>
    @foreach ($staffUsers as $user)
        <tr>
            <td>{{ $user->name }}</td>
            <td>{{ $user->email }}</td>
            <td><a href="/admin/users/{{ $user->id }}/attendances">詳細</a></td>
        </tr>
    @endforeach
    </tbody>
</table>
@endsection
