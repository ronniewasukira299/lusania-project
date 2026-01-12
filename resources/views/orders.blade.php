@extends('layouts.app')

@section('title', 'My Orders')

@section('content')
<div class="container my-5">
  <h2 class="text-warning text-center mb-4">📦 My Orders</h2>
  <div id="myOrders"></div>
</div>
@endsection

@section('scripts')
<script>
// Your original orders script – static for now; later fetch from backend
const orders = JSON.parse(localStorage.getItem("orders")) || [];
const container = document.getElementById("myOrders");

if (orders.length === 0) {
  container.innerHTML = "<p>No orders found.</p>";
}

orders.forEach(order => {
  container.innerHTML += `
    <div class="card bg-dark text-white mb-3">
      <div class="card-body">
        <p><strong>Date:</strong> ${order.date}</p>
        <p><strong>Total:</strong> UGX ${order.total}</p>
      </div>
    </div>
  `;
});
</script>
@endsection