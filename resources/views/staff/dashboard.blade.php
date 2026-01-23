@extends('layouts.app')

@section('title', 'Staff Dashboard')

@section('content')
<div class="container my-5">
  <div class="row mb-4">
    <div class="col-md-8">
      <h2 class="text-warning">👨‍💼 Staff Dashboard</h2>
      <p class="text-muted">Welcome, {{ auth()->user()->name }}</p>
    </div>
    <div class="col-md-4 text-end">
      <!-- Availability Toggle Button -->
      <button type="button" class="btn btn-success btn-lg" id="availability-toggle" data-status="{{ auth()->user()->staffProfile->status ?? 'available' }}">
        <span id="status-text">🟢 Available</span>
      </button>
    </div>
  </div>

  <!-- Assigned Orders Section -->
  <div class="card bg-dark text-white mb-4">
    <div class="card-header bg-warning text-dark">
      <h5 class="mb-0">📦 Assigned Orders</h5>
    </div>
    <div class="card-body">
      @if ($orders->isEmpty())
        <p class="text-center text-muted mb-0">No assigned orders yet. You'll receive notifications when new orders are assigned.</p>
      @else
        @foreach ($orders as $order)
          <div class="card bg-secondary text-white mb-3">
            <div class="card-body">
              <div class="row">
                <div class="col-md-6">
                  <h6>Order #{{ $order->id }}</h6>
                  <p class="mb-1"><strong>Customer:</strong> {{ $order->user->name ?? 'Guest' }}</p>
                  <p class="mb-1"><strong>Phone:</strong> {{ $order->user->email }}</p>
                  <p class="mb-1"><strong>Address:</strong> {{ $order->delivery_address }}</p>
                  <p class="mb-0"><strong>Total:</strong> UGX {{ number_format($order->total_amount) }}</p>
                </div>
                <div class="col-md-6">
                  <p class="mb-1"><strong>Status:</strong> 
                    <span class="badge bg-{{ $order->status === 'assigned' ? 'info' : ($order->status === 'in_transit' ? 'warning' : 'success') }}">
                      {{ ucfirst($order->status) }}
                    </span>
                  </p>
                  <p class="mb-1"><strong>Placed:</strong> {{ $order->created_at->format('M d, Y H:i') }}</p>
                  <p class="mb-3"><strong>Items:</strong> {{ $order->items->count() }}</p>
                  
                  <!-- Order Items -->
                  <div class="mb-3">
                    <strong>Items:</strong>
                    <ul class="small mb-0">
                      @foreach ($order->items as $item)
                        <li>{{ $item->product->name }} × {{ $item->quantity }} — UGX {{ number_format($item->price * $item->quantity) }}</li>
                      @endforeach
                    </ul>
                  </div>

                  <!-- Action Buttons -->
                  @if($order->status === 'assigned')
                    <form method="POST" action="{{ route('orders.start-journey', $order) }}" class="d-inline">
                      @csrf
                      <button type="submit" class="btn btn-primary btn-sm">🚚 Start Journey</button>
                    </form>
                  @endif

                  @if($order->status === 'in_transit')
                    <button type="button" class="btn btn-success btn-sm" onclick="markDelivered({{ $order->id }})">
                      ✅ Mark Delivered
                    </button>
                  @endif
                </div>
              </div>
            </div>
          </div>
        @endforeach
      @endif
    </div>
  </div>

  <!-- Staff Stats -->
  <div class="row">
    <div class="col-md-4 mb-3">
      <div class="card bg-dark text-white">
        <div class="card-body text-center">
          <h4 class="text-warning">{{ $orders->count() }}</h4>
          <p class="mb-0">Assigned Orders</p>
        </div>
      </div>
    </div>
    <div class="col-md-4 mb-3">
      <div class="card bg-dark text-white">
        <div class="card-body text-center">
          <h4 class="text-success">
            {{ (auth()->user()->staffProfile && auth()->user()->staffProfile->assignments) ? auth()->user()->staffProfile->assignments->where('order.status', 'delivered')->count() : 0 }}
          </h4>
          <p class="mb-0">Completed Deliveries</p>
        </div>
      </div>
    </div>
    <div class="col-md-4 mb-3">
      <div class="card bg-dark text-white">
        <div class="card-body text-center">
          <h4 class="text-info">{{ (auth()->user()->staffProfile && auth()->user()->staffProfile->assignments) ? auth()->user()->staffProfile->assignments->where('order.status', 'in_transit')->count() : 0 }}</h4>
          <p class="mb-0">In Transit</p>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
// Staff Availability Toggle
document.getElementById('availability-toggle').addEventListener('click', function() {
  const btn = this;
  const currentStatus = btn.getAttribute('data-status');
  const newStatus = currentStatus === 'available' ? 'unavailable' : 'available';

  fetch('{{ route("staff.toggle-availability") }}', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': '{{ csrf_token() }}'
    },
    body: JSON.stringify({ status: newStatus })
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      btn.setAttribute('data-status', newStatus);
      if (newStatus === 'available') {
        btn.classList.remove('btn-secondary');
        btn.classList.add('btn-success');
        document.getElementById('status-text').textContent = '🟢 Available';
        PusherNotifications.showNotification('✓ Available', 'You are now available to receive orders', 'success');
      } else {
        btn.classList.remove('btn-success');
        btn.classList.add('btn-secondary');
        document.getElementById('status-text').textContent = '🔴 Unavailable';
        PusherNotifications.showNotification('⊗ Unavailable', 'You are now unavailable for orders', 'info');
      }
    }
  })
  .catch(error => console.error('Error:', error));
});

function markDelivered(orderId) {
  if (confirm('Mark this order as delivered?')) {
    fetch(`/orders/${orderId}/mark-delivered`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': '{{ csrf_token() }}'
      }
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        PusherNotifications.showNotification(
          '✅ Order Delivered',
          'Order marked as delivered successfully!',
          'success',
          () => location.reload()
        );
      } else {
        alert('Error: ' + (data.message || 'Failed to mark as delivered'));
      }
    })
    .catch(error => console.error('Error:', error));
  }
}

// Listen for real-time updates - auto-refresh when new order is assigned
document.addEventListener('DOMContentLoaded', function() {
  if (window.PusherNotifications && window.PusherNotifications.initialized) {
    const staffChannel = window.PusherNotifications.pusher.subscribe(`private-orders.staff.{{ auth()->id() }}`);
    
    // When new order is assigned, refresh to show it
    staffChannel.bind('order.assigned', (data) => {
      setTimeout(() => {
        location.reload();
      }, 2000);
    });
  }
});
</script>
@endsection