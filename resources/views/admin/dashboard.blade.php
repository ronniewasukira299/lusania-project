@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="container-fluid my-5">
  <div class="row mb-4">
    <div class="col-12">
      <h2 class="text-warning">📊 Admin Dashboard</h2>
    </div>
  </div>

  <!-- Key Metrics Row -->
  <div class="row mb-4">
    <div class="col-md-3 mb-3">
      <div class="card bg-dark text-white">
        <div class="card-body text-center">
          <h5 class="text-warning">Total Orders</h5>
          <h3 class="text-success">{{ $orders->count() }}</h3>
        </div>
      </div>
    </div>
    <div class="col-md-3 mb-3">
      <div class="card bg-dark text-white">
        <div class="card-body text-center">
          <h5 class="text-warning">Today's Revenue</h5>
          <h3 class="text-success">UGX {{ number_format($orders->where('created_at', '>=', now()->startOfDay())->where('status', 'delivered')->sum('total_amount')) }}</h3>
        </div>
      </div>
    </div>
    <div class="col-md-3 mb-3">
      <div class="card bg-dark text-white">
        <div class="card-body text-center">
          <h5 class="text-warning">Delivered Today</h5>
          <h3 class="text-success">{{ $orders->where('created_at', '>=', now()->startOfDay())->where('status', 'delivered')->count() }}</h3>
        </div>
      </div>
    </div>
    <div class="col-md-3 mb-3">
      <div class="card bg-dark text-white">
        <div class="card-body text-center">
          <h5 class="text-warning">Pending Orders</h5>
          <h3 class="text-warning">{{ $orders->where('status', 'pending')->count() }}</h3>
        </div>
      </div>
    </div>
  </div>

  <!-- Orders Management Section -->
  <div class="card bg-dark text-white mb-4">
    <div class="card-header bg-warning text-dark">
      <h5 class="mb-0">📦 All Orders</h5>
    </div>
    <div class="card-body">
      @if ($orders->isEmpty())
        <p class="text-center text-muted mb-0">No orders yet.</p>
      @else
        <div class="table-responsive">
          <table class="table table-dark table-hover">
            <thead class="table-warning">
              <tr>
                <th>Order ID</th>
                <th>Customer</th>
                <th>Status</th>
                <th>Amount (UGX)</th>
                <th>Assigned To</th>
                <th>Placed</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              @foreach($orders as $order)
                <tr>
                  <td><strong>#{{ $order->id }}</strong></td>
                  <td>{{ $order->user->name ?? 'Guest' }}</td>
                  <td>
                    <span class="badge bg-{{ $order->status === 'delivered' ? 'success' : ($order->status === 'in_transit' ? 'info' : ($order->status === 'cancelled' ? 'danger' : 'warning')) }}">
                      {{ ucfirst($order->status) }}
                    </span>
                  </td>
                  <td>{{ number_format($order->total_amount) }}</td>
                  <td>
                    @if($order->assignment)
                      {{ $order->assignment->staff->name ?? 'N/A' }}
                    @else
                      <span class="text-muted">—</span>
                    @endif
                  </td>
                  <td>{{ $order->created_at->format('M d, Y H:i') }}</td>
                  <td>
                    <button type="button" class="btn btn-sm btn-info" onclick="viewOrder({{ $order->id }})">View</button>
                    @if($order->status !== 'cancelled' && $order->status !== 'delivered')
                      <form method="POST" action="{{ route('orders.cancel', $order) }}" class="d-inline" onsubmit="return confirm('Cancel this order?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger">Cancel</button>
                      </form>
                    @endif
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      @endif
    </div>
  </div>

  <!-- Staff Activity Section -->
  <div class="row">
    <div class="col-12">
      <div class="card bg-dark text-white">
        <div class="card-header bg-warning text-dark">
          <h5 class="mb-0">👥 Staff Activity</h5>
        </div>
        <div class="card-body">
          <div class="row">
            @php
              $staff = \App\Models\User::where('role', 'staff')->get();
              if ($staff->isEmpty()) {
                echo '<p class="text-muted">No staff members yet.</p>';
              }
            @endphp
            @forelse($staff as $member)
              <div class="col-md-4 mb-3">
                <div class="card bg-secondary text-white">
                  <div class="card-body">
                    <h6>{{ $member->name }}</h6>
                    <p class="mb-1 small">
                      <strong>Status:</strong>
                      @php
                        $status = $member->staffProfile->status ?? 'available';
                      @endphp
                      <span class="badge bg-{{ $status === 'available' ? 'success' : ($status === 'in_transit' ? 'info' : 'warning') }}">
                        {{ ucfirst($status) }}
                      </span>
                    </p>
                    @php
                      $completed = $member->staffProfile?->assignments()->whereHas('order', fn($q) => $q->where('status', 'delivered'))->count() ?? 0;
                      $inTransit = $member->staffProfile?->assignments()->whereHas('order', fn($q) => $q->where('status', 'in_transit'))->count() ?? 0;
                    @endphp
                    <p class="mb-1 small"><strong>Completed:</strong> {{ $completed }}</p>
                    <p class="mb-0 small"><strong>In Transit:</strong> {{ $inTransit }}</p>
                  </div>
                </div>
              </div>
            @empty
              <p class="text-muted">No staff members yet.</p>
            @endforelse
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Daily Revenue Chart Section (optional) -->
  <div class="row mt-4">
    <div class="col-12">
      <div class="card bg-dark text-white">
        <div class="card-header bg-warning text-dark">
          <h5 class="mb-0">💰 Revenue Summary</h5>
        </div>
        <div class="card-body">
          <div class="row text-center">
            <div class="col-md-3">
              <h6>Today's Revenue</h6>
              <h4 class="text-success">UGX {{ number_format($orders->where('created_at', '>=', now()->startOfDay())->where('status', 'delivered')->sum('total_amount')) }}</h4>
            </div>
            <div class="col-md-3">
              <h6>This Week</h6>
              <h4 class="text-success">UGX {{ number_format($orders->where('created_at', '>=', now()->startOfWeek())->where('status', 'delivered')->sum('total_amount')) }}</h4>
            </div>
            <div class="col-md-3">
              <h6>This Month</h6>
              <h4 class="text-success">UGX {{ number_format($orders->where('created_at', '>=', now()->startOfMonth())->where('status', 'delivered')->sum('total_amount')) }}</h4>
            </div>
            <div class="col-md-3">
              <h6>Total Revenue</h6>
              <h4 class="text-success">UGX {{ number_format($orders->where('status', 'delivered')->sum('total_amount')) }}</h4>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
function viewOrder(orderId) {
  alert('Order details: #' + orderId);
  // In production, navigate to order details page
}
</script>
@endsection