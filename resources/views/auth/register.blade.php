@extends('layouts.app')

@section('title', 'Sign Up')

@section('content')
<div class="container d-flex justify-content-center align-items-center min-vh-100">
  <div class="card auth-card p-4 text-white shadow-lg" style="max-width:420px; width:100%; background:#1b1b1b; border-radius:16px;">
    <h3 class="text-warning text-center mb-4 fw-bold">Create Account</h3>

    <form method="POST" action="{{ route('register') }}">
      @csrf

      <!-- Username -->
      <div class="mb-3">
        <label class="form-label fw-bold">Username</label>
        <input type="text" name="username" class="form-control @error('username') is-invalid @enderror"
               value="{{ old('username') }}" required autofocus autocomplete="username" placeholder="Choose a username">
        @error('username')
          <div class="invalid-feedback">{{ $message }}</div>
        @enderror
      </div>

      <!-- Email -->
      <div class="mb-3">
        <label class="form-label fw-bold">Email</label>
        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
               value="{{ old('email') }}" required autocomplete="email" placeholder="your@email.com">
        @error('email')
          <div class="invalid-feedback">{{ $message }}</div>
        @enderror
      </div>

      <!-- Password -->
      <div class="mb-3">
        <label class="form-label fw-bold">Password</label>
        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
               required autocomplete="new-password" placeholder="••••••••">
        @error('password')
          <div class="invalid-feedback">{{ $message }}</div>
        @enderror
      </div>

      <!-- Confirm Password -->
      <div class="mb-4">
        <label class="form-label fw-bold">Confirm Password</label>
        <input type="password" name="password_confirmation" class="form-control" required autocomplete="new-password" placeholder="••••••••">
      </div>

      <!-- Hidden role (for secret routes) -->
      <input type="hidden" name="role" value="{{ $role ?? 'customer' }}">

      <button type="submit" class="btn btn-warning w-100 fw-bold py-2">Sign Up</button>
    </form>

    <p class="text-center mt-3 text-light">
      Already have an account? 
      <a href="{{ route('login') }}" class="text-warning fw-bold">Login</a>
    </p>
  </div>
</div>
@endsection