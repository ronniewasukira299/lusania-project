@extends('layouts.app')

@section('title', 'Sign Up')

@section('content')
<div class="container d-flex justify-content-center align-items-center min-vh-100">
  <div class="card auth-card p-4 text-white shadow-lg" style="max-width:420px; width:100%; background:#1b1b1b; border-radius:16px;">
    <h3 class="text-warning text-center mb-4 fw-bold">Create Account</h3>

    @if ($errors->any())
      <div class="alert alert-danger mb-4">
        <strong>Registration failed:</strong>
        <ul class="mb-0">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <form method="POST" action="{{ route('register') }}" id="registerForm" onsubmit="validateForm(event)">
      @csrf

      <!-- Username -->
      <div class="mb-3">
        <label class="form-label fw-bold">Username</label>
        <input type="text" name="username" class="form-control @error('username') is-invalid @enderror"
               value="{{ old('username') }}" required minlength="3" autofocus autocomplete="username" placeholder="Choose a username">
        @error('username')
          <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
      </div>

      <!-- Email -->
      <div class="mb-3">
        <label class="form-label fw-bold">Email</label>
        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
               value="{{ old('email') }}" required autocomplete="email" placeholder="your@email.com">
        @error('email')
          <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
      </div>

      <!-- Password -->
      <div class="mb-3">
        <label class="form-label fw-bold">Password</label>
        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
               required minlength="8" autocomplete="new-password" placeholder="••••••••">
        @error('password')
          <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
      </div>

      <!-- Confirm Password -->
      <div class="mb-4">
        <label class="form-label fw-bold">Confirm Password</label>
        <input type="password" name="password_confirmation" class="form-control @error('password_confirmation') is-invalid @enderror" 
               required minlength="8" autocomplete="new-password" placeholder="••••••••">
        @error('password_confirmation')
          <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
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

<script>
function validateForm(event) {
  const username = document.querySelector('input[name="username"]').value.trim();
  const email = document.querySelector('input[name="email"]').value.trim();
  const password = document.querySelector('input[name="password"]').value;
  const passwordConfirm = document.querySelector('input[name="password_confirmation"]').value;

  if (!username || username.length < 3) {
    event.preventDefault();
    alert('Username is required and must be at least 3 characters');
    return false;
  }

  if (!email || !email.includes('@')) {
    event.preventDefault();
    alert('Valid email is required');
    return false;
  }

  if (!password || password.length < 8) {
    event.preventDefault();
    alert('Password is required and must be at least 8 characters');
    return false;
  }

  if (password !== passwordConfirm) {
    event.preventDefault();
    alert('Passwords do not match');
    return false;
  }

  return true;
}
</script>
@endsection