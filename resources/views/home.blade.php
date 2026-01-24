@extends('layouts.app')

@section('title', 'Caleb\'s Chicken Lusaniya')

@section('styles')
<style>
    body { background-color: #1a1a1a; color: #ffffff; overflow-x: hidden; }
    .home-container { position: relative; min-height: 100vh; }
    .background-watermark {
        background-image: url('{{ asset('images/poster.png') }}');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        opacity: 0.08;
        z-index: -1;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
    }
    .logo-img { max-height: 140px; filter: drop-shadow(0 4px 12px rgba(0,0,0,0.6)); }
    .hero-title { font-size: 3rem; font-weight: 900; text-shadow: 3px 3px 10px rgba(0,0,0,0.7); }
    .hero-subtitle { font-size: 1.3rem; max-width: 800px; margin: 0 auto; text-shadow: 1px 1px 4px rgba(0,0,0,0.6); }
    .btn-custom { padding: 1rem 2.2rem; font-size: 1.15rem; transition: all 0.3s ease; }
    .btn-custom:hover { transform: translateY(-4px); box-shadow: 0 12px 24px rgba(255,193,7,0.4); }
    .action-card {
        background: rgba(0,0,0,0.7);
        backdrop-filter: blur(12px);
        border: 1px solid rgba(255,193,7,0.3);
        border-radius: 20px;
    }
    .menu-img { max-height: 500px; border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.6); }
    .instruction-list li { margin-bottom: 1.2rem; font-size: 1.1rem; }
    @media (max-width: 768px) {
        .hero-title { font-size: 2.2rem; }
        .logo-img { max-height: 100px; }
        .btn-custom { font-size: 1rem; padding: 0.8rem 1.8rem; }
    }
</style>
@endsection

@section('content')
<div class="home-container position-relative">
    <!-- Background Flyer Watermark -->
    <div class="background-watermark"></div>

    <!-- Main Content -->
    <div class="d-flex flex-column min-vh-100">
        <div class="flex-grow-1 d-flex align-items-center justify-content-center py-5">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-12 col-lg-10 text-center">
                        <!-- Logo -->
                        <img src="{{ asset('images/logo.png') }}"
                             alt="Caleb's Chicken Lusaniya Logo"
                             class="logo-img mb-4 img-fluid">

                        <!-- Hero -->
                        <h1 class="hero-title mb-3">Welcome to Caleb's Chicken Lusaniya</h1>
                        <p class="hero-subtitle lead mb-5">
                            Premium fried chicken Lusaniya, crafted with the finest ingredients for an unforgettable taste experience. Fresh, flavorful, and delivered right to your door!
                        </p>

                        <!-- Action Buttons -->
                        <div class="action-card p-5 shadow-lg">
                            <div class="row g-3 justify-content-center">
                                @guest
                                    <div class="col-12 col-sm-6 col-lg-4">
                                        <a href="{{ route('login') }}" class="btn btn-custom btn-warning w-100 fw-bold">
                                            <i class="bi bi-box-arrow-in-right me-2"></i>Login
                                        </a>
                                    </div>
                                    <div class="col-12 col-sm-6 col-lg-4">
                                        <a href="{{ route('register') }}" class="btn btn-custom btn-warning w-100 fw-bold">
                                            <i class="bi bi-person-plus me-2"></i>Create Account
                                        </a>
                                    </div>
                                @else
                                    <div class="col-12 col-sm-6 col-lg-6">
                                        @if(auth()->user()->role === 'admin')
                                            <a href="{{ route('admin.dashboard') }}" class="btn btn-custom btn-warning w-100 fw-bold">
                                                <i class="bi bi-speedometer2 me-2"></i>Admin Dashboard
                                            </a>
                                        @elseif(auth()->user()->role === 'staff')
                                            <a href="{{ route('staff.dashboard') }}" class="btn btn-custom btn-warning w-100 fw-bold">
                                                <i class="bi bi-speedometer2 me-2"></i>Staff Dashboard
                                            </a>
                                        @else
                                            <a href="{{ route('products') }}" class="btn btn-custom btn-warning w-100 fw-bold">
                                                <i class="bi bi-shop me-2"></i>Shop Now
                                            </a>
                                        @endif
                                    </div>
                                @endguest

                                <div class="col-12 col-sm-6 col-lg-4">
                                    <button class="btn btn-custom btn-outline-warning w-100 fw-bold" id="getStartedBtn">
                                        <i class="bi bi-info-circle me-2"></i>Get Started
                                    </button>
                                </div>
                            </div>

                            <!-- Get Started Section -->
                            <div id="getStartedSection" class="mt-5" style="display: none;">
                                <div class="card bg-dark border-warning border-2">
                                    <div class="card-body p-4">
                                        <h4 class="text-warning mb-4 fw-bold">
                                            <i class="bi bi-book me-2"></i>How to Use as a Customer
                                        </h4>
                                        <ol class="text-light mb-0 ps-4">
                                            <li class="mb-3"><strong>Register or Login</strong> — Create your account or sign in.</li>
                                            <li class="mb-3"><strong>Browse Products</strong> — Explore our delicious menu.</li>
                                            <li class="mb-3"><strong>Add to Cart or Buy Now</strong> — Select items and proceed.</li>
                                            <li class="mb-3"><strong>Checkout with Cash on Delivery</strong> — Pay when you receive.</li>
                                            <li><strong>Wait for Delivery</strong> — Staff will deliver, then confirm.</li>
                                        </ol>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Menu Image Section -->
                        <div class="mt-5">
                            <img src="{{ asset('images/menu.png') }}"
                                 alt="Caleb's Chicken Lusaniya Menu"
                                 class="menu-img img-fluid">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const getStartedBtn = document.getElementById('getStartedBtn');
    const getStartedSection = document.getElementById('getStartedSection');

    if (getStartedBtn && getStartedSection) {
        getStartedBtn.addEventListener('click', () => {
            if (getStartedSection.style.display === 'none' || getStartedSection.style.display === '') {
                getStartedSection.style.display = 'block';
                getStartedBtn.innerHTML = '<i class="bi bi-x-circle me-2"></i>Close';
                getStartedSection.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            } else {
                getStartedSection.style.display = 'none';
                getStartedBtn.innerHTML = '<i class="bi bi-info-circle me-2"></i>Get Started';
            }
        });
    }
});
</script>
@endsection