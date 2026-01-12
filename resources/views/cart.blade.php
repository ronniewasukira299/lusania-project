@extends('layouts.app')

@section('title', 'Your Cart')

@section('content')
<nav class="navbar navbar-dark bg-dark px-3">
  <a class="navbar-brand" href="{{ route('home') }}">← Continue Shopping</a>
  <span class="fs-5">🛒 Your Cart</span>
</nav>

<div class="container my-5">
  <div id="cart-items"></div>

  <div class="card bg-dark text-white mt-4">
    <div class="card-body d-flex justify-content-between align-items-center">
      <h4>Total:</h4>
      <h4>UGX <span id="cart-total">0</span></h4>
    </div>
  </div>

  <div class="d-flex justify-content-end gap-3 mt-4">
    <button class="btn btn-outline-light" onclick="clearCart()">Clear Cart</button>
    <button class="btn btn-warning" onclick="checkout()">Proceed to Checkout</button>
  </div>
</div>
@endsection

@section('scripts')
<script>
// Your exact original cart script – no changes needed
let cart = JSON.parse(localStorage.getItem("cart")) || [];
const cartContainer = document.getElementById("cart-items");

function renderCart() {
  cartContainer.innerHTML = "";
  let total = 0;

  if (cart.length === 0) {
    cartContainer.innerHTML = "<p class='text-center'>🛒 Your cart is empty</p>";
    document.getElementById("cart-total").innerText = 0;
    return;
  }

  cart.forEach((item, index) => {
    total += item.price * item.qty;

    cartContainer.innerHTML += `
      <div class="card cart-card bg-dark text-white mb-3">
        <div class="card-body d-flex justify-content-between align-items-center flex-wrap">
          <div>
            <h5>${item.name}</h5>
            <p>UGX ${item.price}</p>
          </div>

          <div class="d-flex align-items-center gap-2">
            <button class="btn btn-sm btn-secondary" onclick="changeQty(${index}, -1)">−</button>
            <span>${item.qty}</span>
            <button class="btn btn-sm btn-secondary" onclick="changeQty(${index}, 1)">+</button>
          </div>

          <button class="btn btn-sm btn-danger" onclick="removeItem(${index})">🗑</button>
        </div>
      </div>
    `;
  });

  document.getElementById("cart-total").innerText = total;
}

function changeQty(index, change) {
  cart[index].qty += change;
  if (cart[index].qty <= 0) cart.splice(index, 1);
  updateCart();
}

function removeItem(index) {
  cart.splice(index, 1);
  updateCart();
}

function clearCart() {
  if (confirm("Clear all items?")) {
    cart = [];
    updateCart();
  }
}

function updateCart() {
  localStorage.setItem("cart", JSON.stringify(cart));
  renderCart();
}

function checkout() {
  localStorage.setItem("checkout", JSON.stringify(cart));
  window.location.href = "{{ route('checkout') }}";
}

renderCart();
</script>
@endsection