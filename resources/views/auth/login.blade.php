@extends('layouts.app')

@section('title', 'Login')

@section('content')
<div class="container d-flex justify-content-center align-items-center min-vh-100">
  <div class="card auth-card p-4 text-white shadow-lg" style="max-width:420px; width:100%; background:#1b1b1b; border-radius:16px;">
    <h3 class="text-warning text-center mb-4 fw-bold">Welcome Back</h3>

    <form method="POST" action="{{ route('login') }}">
      @csrf

      <!-- Username or Email -->
      <div class="mb-4">
        <label class="form-label fw-bold">Username or Email</label>
        <input type="text" name="login" class="form-control @error('login') is-invalid @enderror"
               value="{{ old('login') }}" required autofocus autocomplete="username" placeholder="Username or email">
        @error('login')
          <div class="invalid-feedback">{{ $message }}</div>
        @enderror
      </div>

      <!-- Password -->
      <div class="mb-4">
        <label class="form-label fw-bold">Password</label>
        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
               required autocomplete="current-password" placeholder="••••••••">
        @error('password')
          <div class="invalid-feedback">{{ $message }}</div>
        @enderror
      </div>

      <!-- Remember Me + Forgot Password -->
      <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="form-check">
          <input class="form-check-input" type="checkbox" name="remember" id="remember">
          <label class="form-check-label text-light" for="remember">Remember me</label>
        </div>

        @if (Route::has('password.request'))
          <a class="text-warning small fw-bold" href="{{ route('password.request') }}">
            Forgot your password?
          </a>
        @endif
      </div>

      <button type="submit" class="btn btn-warning w-100 fw-bold py-2">Login</button>
    </form>

    <p class="text-center mt-3 text-light">
      Don't have an account? 
      <a href="{{ route('register') }}" class="text-warning fw-bold">Create one</a>
    </p>
  </div>
</div>
@endsection