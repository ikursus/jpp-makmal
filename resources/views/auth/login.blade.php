@extends('layouts.app')

@section('title', 'Log Masuk')

@section('content')
<div class="login-page">
    <div class="login-card">
        <div class="logo-text">
            <h2>🏛️ JPP Makmal</h2>
            <h3>Sistem Pengurusan Barangan Makmal</h3>
        </div>

        @if($errors->any())
        <div class="alert alert-danger">
            {{ $errors->first() }}
        </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="form-group">
                <label for="email">Emel</label>
                <input type="email" name="email" id="email" class="form-control @error('email') error @enderror" value="{{ old('email') }}" required autofocus placeholder="nama@jpp.gov.my">
            </div>

            <div class="form-group">
                <label for="password">Kata Laluan</label>
                <input type="password" name="password" id="password" class="form-control @error('password') error @enderror" required placeholder="••••••••">
            </div>

            <div class="checkbox-group">
                <input type="checkbox" name="remember" id="remember">
                <label for="remember">Ingat Saya</label>
            </div>

            <button type="submit" class="btn btn-primary">Log Masuk</button>
        </form>

        <p style="text-align: center; margin-top: 20px; font-size: 12px; color: #6b7280;">
            © 2026 JPP Sabah
        </p>
    </div>
</div>
@endsection
