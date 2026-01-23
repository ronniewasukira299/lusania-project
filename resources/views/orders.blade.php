@extends('layouts.app')

@section('title', 'My Orders')

@section('content')
<div class="container my-5">
  <div class="row mb-4">
    <div class="col-12">
      <h2 class="text-warning">📦 My Orders</h2>
      <p class="text-muted">Track your orders here</p>
    </div>
  </div>

  @auth
    @php
      $customerOrders = auth()->user()->orders()->latest()->get();
    @endphp

    @if ($customerOrders->isEmpty())
      <div class="alert alert-info text-center">
        <p class="mb-0">You haven't placed any orders yet. <a href="{{ route('products') }}" class="alert-link">Start shopping now!</a></p>
      </div>
    @else
      @foreach ($customerOrders as $order)
        <div class="card bg-dark text-white mb-4" data-order-id="{{ $order->id }}">
          <div class="card-header bg-warning text-dark">
            <div class="row">
              <div class="col-md-6">
                <h5 class="mb-0">Order #{{ $order->id }}</h5>
              </div>
              <div class="col-md-6 text-end">
                <span class="badge bg-{{ $order->status === 'delivered' ? 'success' : ($order->status === 'in_transit' ? 'info' : ($order->status === 'cancelled' ? 'danger' : 'warning')) }} order-status-badge">
                  {{ ucfirst($order->status) }}
                </span>
              </div>
            </div>
          </div>
          <div class="card-body">
            <div class="row mb-3">
              <div class="col-md-6">
                <p class="mb-1"><strong>Placed:</strong> {{ $order->created_at->format('M d, Y H:i') }}</p>
                <p class="mb-1"><strong>Delivery Address:</strong> {{ $order->delivery_address }}</p>
                <p class="mb-1"><strong>Payment Method:</strong> Cash on Delivery</p>
              </div>
              <div class="col-md-6">
                <p class="mb-1"><strong>Items:</strong> {{ $order->items->count() }}</p>
                <p class="mb-1"><strong>Subtotal:</strong> UGX {{ number_format($order->total_amount - 5000) }}</p>
                <p class="mb-1"><strong>Delivery Fee:</strong> UGX 5,000</p>
              </div>
            </div>

            <!-- Assignment Info -->
            @if($order->assignment && $order->assignment->staff)
              <div class="row mb-3 bg-secondary p-3 rounded">
                <div class="col-md-12">
                  <p class="mb-1"><strong>👨‍💼 Assigned to:</strong> <span class="text-warning">{{ $order->assignment->staff->name }}</span></p>
                  <p class="mb-1"><strong>📞 Staff Status:</strong> <span class="badge bg-info">{{ ucfirst($order->assignment->staff->staffProfile->status ?? 'unavailable') }}</span></p>
                </div>
              </div>
            @elseif($order->status !== 'cancelled' && $order->status !== 'delivered')
              <div class="row mb-3 bg-warning p-3 rounded">
                <div class="col-md-12">
                  <p class="mb-0"><strong>🔄 Status:</strong> Waiting for staff assignment...</p>
                </div>
              </div>
            @endif

            <!-- Order Items -->
            <div class="mb-3">
              <h6 class="text-warning">📋 Items Ordered:</h6>
              <div class="ms-3">
                @foreach ($order->items as $item)
                  <p class="mb-1 small">
                    {{ $item->product->name }} × {{ $item->quantity }} — UGX {{ number_format($item->price * $item->quantity) }}
                  </p>
                @endforeach
              </div>
            </div>

            <!-- Order Total -->
            <div class="alert alert-info mb-3">
              <h5 class="mb-0">💰 Total Amount: UGX {{ number_format($order->total_amount) }}</h5>
            </div>

            <!-- Actions based on Order Status -->
            <div class="row">
              <div class="col-12">
                @if ($order->status === 'in_transit')
                  <button type="button" class="btn btn-success btn-lg" onclick="confirmDelivery({{ $order->id }})">
                    ✅ Confirm Delivery Received
                  </button>
                  <small class="d-block mt-2 text-muted">Click this button once you've received your order</small>
                @elseif ($order->status === 'delivered')
                  <div class="alert alert-success mb-0">
                    <strong>✓ Delivered!</strong> Thank you for your order. We hope you enjoyed it!
                  </div>
                @elseif ($order->status === 'cancelled')
                  <div class="alert alert-danger mb-0">
                    <strong>✗ Cancelled</strong> - This order has been cancelled.
                  </div>
                @else
                  <div class="alert alert-info mb-0">
                    <strong>⏳ {{ ucfirst($order->status) }}</strong> - Your order is being prepared.
                  </div>
                @endif
              </div>
            </div>
          </div>
        </div>
      @endforeach
    @endif
  @else
    <div class="alert alert-warning text-center">
      <p class="mb-0">Please <a href="{{ route('login') }}" class="alert-link">login</a> to view your orders</p>
    </div>
  @endauth
</div>

<script>
function confirmDelivery(orderId) {
  if (confirm('Confirm that you have received this order?')) {
    fetch(`/orders/${orderId}/customer-confirm-delivery`, {
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
          '✅ Delivery Confirmed',
          'Thank you! Your order has been marked as delivered.',
          'success',
          () => location.reload()
        );
      } else {
        alert('Error: ' + (data.message || 'Failed to confirm delivery'));
      }
    })
    .catch(error => console.error('Error:', error));
  }
}

// Listen for order updates in real-time
document.addEventListener('DOMContentLoaded', function() {
  // Subscribe to order-specific channels if Pusher is initialized
  if (window.PusherNotifications && window.PusherNotifications.initialized) {
    const orderIds = document.querySelectorAll('[data-order-id]');
    orderIds.forEach(el => {
      const orderId = el.getAttribute('data-order-id');
      const channel = window.PusherNotifications.pusher.subscribe(`private-orders.${orderId}`);
      
      // Update UI when order status changes
      channel.bind('order.in_transit', (data) => {
        updateOrderStatus(orderId, 'in_transit');
      });
      
      channel.bind('order.delivered', (data) => {
        updateOrderStatus(orderId, 'delivered');
      });
    });
  }
});

function updateOrderStatus(orderId, status) {
  const statusBadge = document.querySelector(`[data-order-id="${orderId}"] .order-status-badge`);
  if (statusBadge) {
    const badgeClass = status === 'delivered' ? 'bg-success' : 'bg-info';
    statusBadge.textContent = status.charAt(0).toUpperCase() + status.slice(1);
    statusBadge.className = `badge ${badgeClass}`;
  }
}
</script>
@endsection