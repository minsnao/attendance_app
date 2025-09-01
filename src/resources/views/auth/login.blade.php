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
    <h2>ログイン</h2>
    <form method="POST" action="{{ route('login') }}">
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
            <button type="submit">ログインする</button>
        </div>
    </form>
    <a href="{{ route('register') }}" class="other__btn">会員登録はこちら</a>




    <!-- 開発用 -->
    <br><br><br><br><br><br><br><br><br><br><br><a href="/admin/login">admin</a>
</div>
@endsection