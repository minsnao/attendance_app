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
    <h2>会員登録</h2>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div class="form__group">
            <label for="name">ユーザー</label>
            <input type="text" name="name" value="{{ old('name') }}">
        </div>
        <div class="form__group">
            <label for="email">メールアドレス</label>
            <input type="email" name="email" value="{{ old('email') }}">
        </div>
        <div class="form__group">
            <label for="password">パスワード</label>
            <input type="password" name="password">
        </div>
        <div class="form__group">
            <label for="password">確認パスワード</label>
            <input type="password" name="password_confirmation">
        </div>
        <div class="form__btn">
            <button type="submit">登録する</button>
        </div>
    </form>
    <a href="{{ route('login') }}" class="other__btn">ログインはこちら</a>
</div>

@endsection