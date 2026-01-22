admin dashboard 
@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="container my-5">
  <h2 class="text-warning text-center mb-4">📊 Admin Orders Dashboard</h2>

  @if ($orders->isEmpty())
    <p class="text-center text-white">No orders yet.</p>
  @else
    @foreach ($orders as $order)
      <div class="card bg-dark text-white mb-3">
        <div class="card-body">
          <h5>Order #{{ $order->id }}</h5>
          <p><strong>Customer:</strong> {{ $order->user->name ?? 'Guest' }}</p>
          <p><strong>Status:</strong> {{ ucfirst($order->status) }}</p>
          <p><strong>Address:</strong> {{ $order->delivery_address }}</p>
          <p><strong>Total:</strong> UGX {{ number_format($order->total_amount) }}</p>
          <p><strong>Placed:</strong> {{ $order->created_at->format('M d, Y H:i') }}</p>
          @if($order->assignment)
            <p><strong>Assigned to:</strong> {{ $order->assignment->staff->name ?? 'N/A' }}</p>
          @endif

          <!-- Cancel button -->
          @if($order->status != 'cancelled')
            <form method="POST" action="{{ route('orders.cancel', $order) }}" class="mt-3">
              @csrf
              @method('DELETE')
              <button type="submit" class="btn btn-danger btn-sm">Cancel Order</button>
            </form>
          @endif
        </div>
      </div>
    @endforeach
  @endif
</div>
@endsection