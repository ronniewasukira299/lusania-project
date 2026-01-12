@extends('layouts.app')

@section('title', 'Staff Dashboard')

@section('content')
<div class="container my-5">
  <h2 class="text-warning text-center mb-4">📦 Assigned Orders</h2>
  @forelse($orders as $order)
  <div class="card bg-dark text-white mb-3">
    <div class="card-body">
      <h5>Order #{{ $order->id }}</h5>
      <p>Status: {{ $order->status }}</p>
      <p>Address: {{ $order->delivery_address }}</p>
      <form method="POST" action="{{ route('orders.start-journey', $order) }}">
        @csrf
        <button type="submit" class="btn btn-warning">Start Journey</button>
      </form>
    </div>
  </div>
  @empty
  <p>No assigned orders.</p>
  @endforelse
</div>
@endsection