@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="container my-5">
  <h2 class="text-warning text-center mb-4">📊 Admin Orders Dashboard</h2>
  <div id="orders"></div>
  <button class="btn btn-danger mt-4" onclick="clearOrders()">Clear All Orders</button>
</div>
@endsection

@section('scripts')
<script>
// Your original admin script – later replace with real DB data
const orders = JSON.parse(localStorage.getItem("orders")) || [];
const container = document.getElementById("orders");

if (orders.length === 0) {
  container.innerHTML = "<p>No orders yet.</p>";
}

orders.forEach((order, i) => {
  let items = order.items.map(it => `${it.name} × ${it.qty}`).join("<br>");

  container.innerHTML += `
    <div class="card bg-dark text-white mb-3">
      <div class="card-body">
        <h5>Order #${i + 1}</h5>
        <p><strong>Name:</strong> ${order.name}</p>
        <p><strong>Phone:</strong> ${order.phone}</p>
        <p><strong>Address:</strong> ${order.address}</p>
        <p><strong>Payment:</strong> ${order.payment}</p>
        <p><strong>Items:</strong><br>${items}</p>
        <p><strong>Total:</strong> UGX ${order.total}</p>
        <small>${order.date}</small>
      </div>
    </div>
  `;
});

function clearOrders() {
  if (confirm("Clear all orders?")) {
    localStorage.removeItem("orders");
    location.reload();
  }
}
</script>
@endsection