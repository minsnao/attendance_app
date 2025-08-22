@extends('layouts.app')

@section('content')

@if ($errors->any())
    <div class="error__msg">
        @foreach ($errors->all() as $error)
            <p>{{ $error }}</p>
        @endforeach
    </div>
@endif

<div class="input__form">
    <h2>管理者ログイン</h2>
    <form method="POST" action="/admin/login">
        @csrf
        <div class="form__group">
            <label for="email">メールアドレス</label>
            <input type="email" name="email" value="{{ old('email') }}">
        </div>
        <div class="form__group">
            <label for="password">パスワード</label>
            <input type="password" name="password">
        </div>
        <div class="form__btn">
            <button type="submit">管理者にログインする</button>
        </div>
    </form>
</div>

@endsection