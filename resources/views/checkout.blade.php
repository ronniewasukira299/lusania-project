@extends('layouts.app')

@section('title', 'Checkout')

@section('content')
<nav class="navbar navbar-dark bg-dark px-3">
  <a class="navbar-brand" href="{{ route('cart') }}">← Back to Cart</a>
  <span class="fs-5">Checkout</span>
</nav>

<div class="container my-5">
  <div class="row g-4">
    <!-- Order Summary (black background) -->
    <div class="col-md-6">
      <div class="card bg-dark text-white">
        <div class="card-body">
          <h4 class="mb-3">🧾 Order Summary</h4>
          <div id="order-items" class="mb-3"></div>
          <hr class="bg-light">
          <p class="mb-1">Subtotal: UGX <span id="sub-total">0</span></p>
          <p class="mb-1">Delivery: UGX <span id="delivery-fee">5000</span></p>
          <hr class="bg-light">
          <h5 class="mb-0">Overall Total: UGX <span id="order-total">0</span></h5>
        </div>
      </div>
    </div>

    <!-- Customer Details (dark background, white labels) -->
    <div class="col-md-6">
      <div class="card bg-dark text-white">
        <div class="card-body">
          <h4 class="mb-4">📋 Customer Details</h4>

          <form id="checkout-form">
            <div class="mb-3">
              <label class="form-label fw-bold text-white">Full Name</label>
              <input type="text" id="name" class="form-control" required>
            </div>

            <div class="mb-3">
              <label class="form-label fw-bold text-white">Phone Number</label>
              <input type="tel" id="phone" class="form-control" required>
            </div>

            <div class="mb-3">
              <label class="form-label fw-bold text-white">Delivery Address</label>
              <textarea id="address" class="form-control" rows="3" required></textarea>
            </div>

            <div class="mb-4">
              <label class="form-label fw-bold text-white">Payment Method</label>
              <select class="form-select" id="payment" required>
                <option value="cod">Cash on Delivery</option>
              </select>
            </div>

            <button type="submit" class="btn btn-warning w-100 btn-lg">
              📤 Place Order via WhatsApp
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script>
// Your checkout logic (unchanged)
const checkoutItems = JSON.parse(localStorage.getItem("checkout")) || [];
const orderContainer = document.getElementById("order-items");
let total = 0;
const deliveryFee = 5000;

if (checkoutItems.length === 0) {
  orderContainer.innerHTML = "<p class='text-center text-muted'>No items in checkout. Add from cart.</p>";
  document.getElementById("sub-total").innerText = 0;
  document.getElementById("order-total").innerText = 0;
  document.getElementById("checkout-form").style.display = "none";
} else {
  checkoutItems.forEach(item => {
    total += item.price * item.qty;
    orderContainer.innerHTML += `
      <p class="mb-1">${item.name} × ${item.qty} — UGX ${item.price * item.qty}</p>
    `;
  });
  document.getElementById("sub-total").innerText = total;
  document.getElementById("order-total").innerText = total + deliveryFee;

  document.getElementById("checkout-form").addEventListener("submit", e => {
    e.preventDefault();

    const name = document.getElementById("name").value;
    const phone = document.getElementById("phone").value;
    const address = document.getElementById("address").value;
    const payment = document.getElementById("payment").value;

    let message = `*🍗 NEW CHICKEN LUSANIA ORDER 🍗*%0A%0A
👤 Name: ${name}%0A
📞 Phone: ${phone}%0A
📍 Address: ${address}%0A
💳 Payment: ${payment}%0A%0A
🧾 *Order Details:*%0A`;

    checkoutItems.forEach(item => {
      message += `• ${item.name} × ${item.qty} — UGX ${item.price * item.qty}%0A`;
    });

    message += `%0A🚚 Delivery: UGX ${deliveryFee}%0A
💰 *Total Payable:* UGX ${total + deliveryFee}`;

    const whatsappNumber = "256751438976";
    window.open(`https://wa.me/${whatsappNumber}?text=${encodeURIComponent(message)}`, "_blank");

    localStorage.removeItem("cart");
    localStorage.removeItem("checkout");
    window.location.href = "{{ route('success') }}";
  });
}
</script>
@endsection