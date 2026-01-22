<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Events\OrderAssigned;
use App\Events\OrderInTransit;
use App\Events\OrderDelivered;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Services\StaffAssignmentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        ], [
            'items.required' => 'Please add items to your order',
            'items.json' => 'Invalid items format',
        ]);

        $items = json_decode($validated['items'], true);
        $validationError = $this->validateAndParseItems($items);
        
        if ($validationError) {
            return response()->json(['success' => false, 'message' => $validationError], 400);
        }

        $order = $this->createOrder($validated, $items);
        app(StaffAssignmentService::class)->assign($order);
        broadcast(new OrderAssigned($order));

        return $request->expectsJson()
            ? response()->json(['success' => true, 'order_id' => $order->id])
            : redirect()->route('success')->with('success', 'Order placed! We will contact you.');
    }

    private function validateAndParseItems($items): ?string
    {
        if (!is_array($items) || empty($items)) {
            return 'No items in order. Add items from cart before placing order.';
        }
        
        foreach ($items as $item) {
            if (!isset($item['product_id']) || !isset($item['qty'])) {
                return 'Invalid item format';
            }
        }
        
        return null;
    }

    private function createOrder(array $validated, array $items): Order
    {
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

        return $order;
    }

    // Mark order as delivered by staff
    public function markDelivered(Order $order)
    {
        // Verify it's the assigned staff
        if ($order->assignment->staff_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        if ($order->status !== 'in_transit') {
            return response()->json(['success' => false, 'message' => 'Order not in transit'], 400);
        }

        $order->update(['status' => 'delivered']);
        
        // Mark staff as available again
        Auth::user()->staffProfile->update(['status' => 'available']);

        // Broadcast order delivered event
        broadcast(new OrderDelivered($order));

        return response()->json(['success' => true, 'message' => 'Order marked as delivered']);
    }

    // Toggle staff availability
    public function toggleAvailability(Request $request)
    {
        $user = Auth::user();
        $staffProfile = $user->staffProfile;

        if (!$staffProfile) {
            return response()->json(['success' => false, 'message' => 'Staff profile not found'], 404);
        }

        $newStatus = $request->input('status', 'available');
        
        if (!in_array($newStatus, ['available', 'unavailable'])) {
            return response()->json(['success' => false, 'message' => 'Invalid status'], 400);
        }

        $staffProfile->update(['status' => $newStatus]);

        return response()->json(['success' => true, 'status' => $newStatus]);
    }

    // Start journey (staff sets order as in transit)
    public function startJourney(Order $order)
    {
        // Verify it's the assigned staff
        if ($order->assignment->staff_id !== Auth::id()) {
            return back()->with('error', 'Unauthorized');
        }

        if ($order->status !== 'assigned') {
            return back()->with('error', 'Order not ready for transit');
        }

        $order->update(['status' => 'in_transit']);
        Auth::user()->staffProfile->update(['status' => 'in_transit']);
        
        // Broadcast order in transit event
        broadcast(new OrderInTransit($order));
        
        return back()->with('success', 'Journey started! Customer has been notified.');
    }

    // Cancel order (admin only)
    public function cancel(Order $order)
    {
        $order->update(['status' => 'cancelled']);
        
        // Mark staff as available if they were assigned
        if ($order->assignment) {
            $order->assignment->staff->staffProfile->update(['status' => 'available']);
        }

        return back()->with('success', 'Order cancelled!');
    }

    // Customer confirms delivery
    public function customerConfirmDelivery(Order $order)
    {
        // Verify it's the customer's order
        if ($order->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        if ($order->status !== 'in_transit') {
            return response()->json(['success' => false, 'message' => 'Order not in transit'], 400);
        }

        $order->update(['status' => 'delivered']);

        // Broadcast order delivered event
        broadcast(new OrderDelivered($order));

        return response()->json(['success' => true, 'message' => 'Thank you for confirming delivery!']);
    }
}
