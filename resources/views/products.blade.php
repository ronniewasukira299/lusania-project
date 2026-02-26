@extends('layouts.app')

@section('title', 'Products')

@section('content')
<div class="container my-5">
    <h2 class="text-warning text-center mb-4">🍗 Our Chicken Lusania</h2>

    <div class="row g-4">
        @forelse($products as $product)
        <div class="col-12 col-sm-6 col-md-4">
            <div class="card bg-dark text-white product-card h-100"
                 data-id="{{ $product->id }}"
                 data-name="{{ $product->name }}"
                 data-price="{{ $product->price }}">

                <img src="{{ $product->image ? asset('images/' . $product->image) : asset('images/st.jpeg') }}"
                     class="card-img-top"
                     alt="{{ $product->name }}"
                     style="height: 260px; object-fit: cover;">

                <div class="card-body d-flex flex-column text-center">
                    <h5 class="card-title">{{ $product->name }}</h5>
                    <p class="text-muted small flex-grow-1" style="-webkit-line-clamp:2; -webkit-box-orient:vertical; display:-webkit-box; overflow:hidden;">{{ $product->description }}</p>
                    <p class="fw-bold mb-3">UGX {{ number_format($product->price) }}</p>

                    <!-- Quantity selector -->
                    <div class="d-flex justify-content-center align-items-center mb-3 gap-2">
                        <button type="button" class="btn btn-sm btn-secondary qty-btn" onclick="changeQty(this, -1)">−</button>
                        <span class="qty-display fw-bold" data-qty="1">1</span>
                        <button type="button" class="btn btn-sm btn-secondary qty-btn" onclick="changeQty(this, 1)">+</button>
                    </div>

                    <!-- Buttons -->
                    <div class="d-flex justify-content-center gap-2">
                        <button class="btn btn-secondary add-to-cart">🛒 Add to Cart</button>
                        <button class="btn btn-warning buy-now">⚡ Buy Now</button>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <p class="text-center text-white">No products available right now.</p>
        @endforelse
    </div>
</div>
@endsection

@section('scripts')
<script>
// Quantity control
function changeQty(btn, change) {
    const display = btn.parentElement.querySelector('.qty-display');
    let qty = parseInt(display.dataset.qty) + change;
    if (qty < 1) qty = 1;
    display.dataset.qty = qty;
    display.innerText = qty;
}

// Cart logic
let cart = JSON.parse(localStorage.getItem("cart")) || [];

// Add to Cart
document.querySelectorAll(".add-to-cart").forEach(btn => {
    btn.addEventListener("click", e => {
        const card = e.target.closest(".product-card");
        const qty = parseInt(card.querySelector('.qty-display').dataset.qty);
        const product = {
            id: card.dataset.id,
            name: card.dataset.name,
            price: parseInt(card.dataset.price),
            qty: qty
        };

        const existing = cart.find(item => item.id === product.id);
        if (existing) {
            existing.qty += qty;
        } else {
            cart.push(product);
        }

        localStorage.setItem("cart", JSON.stringify(cart));
        alert("Added to cart!");
        updateCartCount();
    });
});

// Buy Now – replaces cart and goes to checkout
document.querySelectorAll(".buy-now").forEach(btn => {
    btn.addEventListener("click", e => {
        const card = e.target.closest(".product-card");
        const qty = parseInt(card.querySelector('.qty-display').dataset.qty);
        const product = {
            id: card.dataset.id,
            name: card.dataset.name,
            price: parseInt(card.dataset.price),
            qty: qty
        };

        cart = [product];
        localStorage.setItem("cart", JSON.stringify(cart));
        localStorage.setItem("checkout", JSON.stringify(cart));

        window.location.href = "{{ route('checkout') }}";
    });
});

function updateCartCount() {
    const countEl = document.getElementById("cart-count");
    if (countEl) {
        countEl.innerText = cart.reduce((sum, item) => sum + item.qty, 0);
    }
}

updateCartCount();
</script>
@endsection
