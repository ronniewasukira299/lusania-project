<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', "Caleb's Chicken Lusania")</title>

    <!-- Bootstrap CDN (fastest for testing) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Your custom style (if you already copied it) -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">

    <!-- Pusher Library for Real-time Notifications -->
    <script src="https://js.pusher.com/8.2/pusher.min.js"></script>

    @yield('styles')
</head>
<body>

<!-- Minimal navbar just for testing -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark py-3">
    <div class="container">
        <a class="navbar-brand" href="{{ url('/') }}">Caleb'S Chicken Lusania</a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('home') }}">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('products') }}">Products</a>
                </li>
                @auth
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('my-orders') }}">Orders</a>
                </li>
                @endauth
                <li class="nav-item">
                    <a class="nav-link position-relative" href="{{ route('cart') }}">
                        🛒 Cart
                        <span id="cart-count" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">0</span>
                    </a>
                </li>
            </ul>
        </div>
        </div>
    </div>
</nav>

<!-- Main content -->
<main class="container my-5">
    @yield('content')
</main>

<!-- Footer (minimal) -->
<footer class="bg-dark text-white pt-5 pb-3 mt-5">
  <div class="container">
    <div class="row">
      <div class="col-md-4 mb-4">
        <h5 class="text-warning">Caleb's Chicken Lusaniya</h5>
        <p>Premium Fried & Chicken combining all foods, and quality since 2025.</p>
      </div>
      <div class="col-md-4 mb-4">
        <h5 class="text-warning">Quick Links</h5>
        <ul class="list-unstyled">
          <li><a href="{{ route('home') }}" class="text-white text-decoration-none">Home</a></li>
          <li><a href="{{ route('products') }}" class="text-white text-decoration-none">Products</a></li>
        </ul>
      </div>
      <div class="col-md-4 mb-4">
        <h5 class="text-warning">Contact Us</h5>
        <p>Email: support@jcalebschickenlusaniya.com</p>
        <p>Phone: +256 751438976 /+256 769895805</p>
        <p>Address: Wandegeya Market Southwing ground floor</p>
      </div>
    </div>
    <hr class="bg-secondary">
    <div class="d-flex justify-content-between align-items-center">
      <p class="mb-0">&copy; 2026 Caleb's Chicken Lusaniya. All rights reserved.</p>
      @auth
      <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="btn btn-danger btn-sm">Logout</button>
      </form>
      @endauth
    </div>
  </div>
</footer>
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- Pusher Notifications Utility -->
<script src="{{ asset('js/pusher-notifications.js') }}"></script>

<!-- Initialize Pusher Notifications -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Pusher with credentials from config
        PusherNotifications.init('{{ config("services.pusher.key") }}', '{{ config("services.pusher.options.cluster") }}');

        // Set up notifications based on user role
        @auth
            @if(auth()->user()->role === 'customer')
                PusherNotifications.subscribeToCustomerOrders({{ auth()->id() }});
            @elseif(auth()->user()->role === 'staff')
                PusherNotifications.subscribeToStaffOrders({{ auth()->id() }});
            @elseif(auth()->user()->role === 'admin')
                PusherNotifications.subscribeToAdminOrders();
            @endif
        @endauth
    });
</script>

@yield('scripts')

</body>
</html>
