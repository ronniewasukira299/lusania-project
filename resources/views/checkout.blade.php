check-out changes
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
          <h4 class="mb-3">📋 Customer Details</h4>

          <form id="checkout-form" method="POST" action="{{ route('orders.store') }}">
            @csrf

            <!-- Hidden items for backend -->
            <input type="hidden" name="items" id="hidden-items" value="">

            <div class="mb-3">
              <label class="form-label fw-bold text-white">Full Name</label>
              <input type="text" name="name" id="name" class="form-control" required>
            </div>

            <div class="mb-3">
              <label class="form-label fw-bold text-white">Phone Number</label>
              <input type="tel" name="phone" id="phone" class="form-control" required>
            </div>

            <div class="mb-3">
              <label class="form-label fw-bold text-white">Delivery Address</label>
              <textarea name="address" id="address" class="form-control" rows="3" required></textarea>
            </div>

            <div class="mb-4">
              <label class="form-label fw-bold text-white">Payment Method</label>
              <select name="payment_method" class="form-select" id="payment" required>
                <option value="cod">Cash on Delivery</option>
              </select>
            </div>

            <!-- Buttons: System + WhatsApp -->
            <div class="d-flex gap-2 mb-4">
              <button type="submit" class="btn btn-primary w-50 fw-bold">📦 Place Order in System</button>
              <button type="button" class="btn btn-warning w-50 fw-bold" id="whatsapp-button">📤 Place via WhatsApp</button>
            </div>

            <!-- Disclaimer -->
            <div class="alert alert-warning text-dark p-3">
              <strong>Important:</strong> Please choose <em>either</em> "Place Order in System" or "Place via WhatsApp" — not both. If you place the same order twice by mistake, contact us, and an admin will cancel the duplicate.
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script>
// Checkout logic (unchanged)
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

  //For system orders
  document.getElementById("hidden-items").value = JSON.stringify(checkoutItems.map(item =>
    ({
      product_id: item.id,
      qty: item.qty
    })
  ));
}

// WhatsApp button (separate from form submit)
document.getElementById("whatsapp-button").addEventListener("click", () => {
  const name = document.getElementById("name").value;
  const phone = document.getElementById("phone").value;
  const address = document.getElementById("address").value;
  const payment = document.getElementById("payment").value;

  let message = `*🍗 NEW CHICKEN LUSANIA ORDER 🍗*\n\n👤 Name: ${name}\n📞 Phone: ${phone}\n📍 Address: ${address}\n💳 Payment: ${payment}\n\n🧾 *Order Details:*\n`;

  checkoutItems.forEach(item => {
    message += `• ${item.name} × ${item.qty} — UGX ${item.price * item.qty}\n`;
  });

  message += `\n🚚 Delivery: UGX ${deliveryFee}\n💰 *Total Payable:* UGX ${total + deliveryFee}`;

  const whatsappNumber = "256751438976";
  window.open(`https://wa.me/${whatsappNumber}?text=${encodeURIComponent(message)}`, "_blank");

  localStorage.removeItem("cart");
  localStorage.removeItem("checkout");
  window.location.href = "{{ route('success') }}?method=whatsapp";
});

// Form submit handles system order with AJAX
document.getElementById("checkout-form").addEventListener("submit", function(e) {
  e.preventDefault();
  const name = document.getElementById("name").value.trim();
  const phone = document.getElementById("phone").value.trim();
  const address = document.getElementById("address").value.trim();
  const payment = document.getElementById("payment").value;
  const items = document.getElementById("hidden-items").value;
  if (!items || items === '[]' || checkoutItems.length === 0) {
    alert('No items in order. Please add items from cart.');
    return;
  }
  if (!name || !phone || !address) {
    alert('Please fill in all fields.');
    return;
  }
  
  // Validate inputs
  if (!name || !phone || !address) {
    alert('Please fill in all required fields');
    return;
  }
  
  // Disable button to prevent double submission
  const submitBtn = this.querySelector('button[type="submit"]');
  submitBtn.disabled = true;
  submitBtn.textContent = '⏳ Processing...';
  
  fetch('{{ route("orders.store") }}', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': '{{ csrf_token() }}'
    },
    body: JSON.stringify({
      name: name,
      phone: phone,
      address: address,
      payment_method: payment,
      items: items
    })
  })
  .then(response => {
    if (!response.ok) {
      return response.json().then(data => {
        throw new Error(data.message || 'Failed to place order');
      });
    }
    return response.json();
  })
  .then(data => {
    if (data.success) {
      localStorage.removeItem("cart");
      localStorage.removeItem("checkout");
      // Show success message and redirect
      const successMsg = document.createElement('div');
      successMsg.className = 'alert alert-success position-fixed top-0 start-0 w-100 rounded-0';
      successMsg.style.zIndex = '9999';
      successMsg.textContent = '✅ Order placed successfully! Redirecting...';
      document.body.prepend(successMsg);
      setTimeout(() => {
        window.location.href = '{{ route("success") }}';
      }, 1500);
    } else {
      alert('Error: ' + (data.message || 'Failed to place order'));
      submitBtn.disabled = false;
      submitBtn.textContent = '📦 Place Order in System';
    }
  })
  .catch(error => {
    console.error('Error:', error);
    // Don't show generic error if order was created (user can check /orders)
    alert('There was an issue, but your order may have been placed. Check your Orders page to verify.');
    submitBtn.disabled = false;
    submitBtn.textContent = '📦 Place Order in System';
    // Still redirect to orders to check
    setTimeout(() => {
      window.location.href = '{{ route("my-orders") }}';
    }, 2000);
  });
});
</script>
@endsection