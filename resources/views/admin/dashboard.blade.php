@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="container my-5">
  <h2 class="text-warning text-center mb-4">📊 Admin Orders Dashboard</h2>

  @if ($orders->isEmpty())
    <p class="text-center text-white">No orders yet.</p>
  @else
    @foreach($orders as $order)
      <div class="card bg-dark text-white mb-3">
        <div class="card-body">
          <h5>Order #{{ $order->id }}</h5>
          <p><strong>Customer:</strong> {{ $order->user->name ?? 'Guest' }}</p>
          <p><strong>Status:</strong> {{ ucfirst($order->status) }}</p>
          <p><strong>Address:</strong> {{ $order->delivery_address }}</p>
          <p><strong>Total:</strong> UGX {{ number_format($order->total_amount) }}</p>
          <p><strong>Placed:</strong> {{ $order->created_at->format('M d, Y H:i') }}</p>
          @if($order->assignment)
            <p><strong>Assigned to Staff:</strong> {{ $order->assignment->staff->user->name ?? 'N/A' }}</p>
          @endif
        </div>
      </div>
    @endforeach
  @endif

  <!-- Optional: Clear all orders button (admin only - use with caution) -->
  <form method="POST" action="{{ route('admin.clear-orders') }}" onsubmit="return confirm('Clear ALL orders? This cannot be undone.');">
    @csrf
    @method('DELETE')
    <button type="submit" class="btn btn-danger mt-4">Clear All Orders</button>
  </form>
</div>
@endsection