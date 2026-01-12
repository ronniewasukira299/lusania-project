<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function store(Request $request)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'phone' => 'required|string|max:20',
        'address' => 'required|string|max:255',
        'payment_method' => 'required|in:cod',
        'items' => 'required|json',
    ]);

    $items = json_decode($validated['items'], true);

    $order = Order::create([
        'user_id' => auth()->id(),
        'delivery_address' => $validated['address'],
        'total_amount' => 0,
        'status' => 'pending',
    ]);

    foreach ($items as $item) {
        $product = Product::findOrFail($item['product_id']);
        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => $item['qty'],
            'price' => $product->price,
        ]);
        $order->increment('total_amount', $product->price * $item['qty']);
    }

    // Auto-assign staff (from your service)
    app(StaffAssignmentService::class)->assign($order);

    // Clear local storage
    return redirect()->route('success')->with('success', 'Order placed! We will contact you.');
}
}