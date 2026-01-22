# API & Events Documentation

## Overview

This document provides technical details about the order management system's API endpoints and broadcasting events.

## Order Management Endpoints

### Place Order (Customer)
**POST** `/orders`

Creates a new order and automatically assigns available staff.

**Authentication:** Required (customer role)

**Request Body:**
```json
{
    "name": "Customer Name",
    "phone": "+256751234567",
    "address": "123 Main Street",
    "payment_method": "cod",
    "items": "[{\"product_id\":1,\"qty\":2},{\"product_id\":3,\"qty\":1}]"
}
```

**Response (JSON):**
```json
{
    "success": true,
    "order_id": 42
}
```

**Events Triggered:**
- `OrderAssigned` - Broadcasts to customer, assigned staff, and admins

---

### Start Delivery (Staff)
**POST** `/orders/{order}/start-journey`

Staff marks order as in transit.

**Authentication:** Required (staff role)

**Response:**
- Redirect with success message
- Shows "Journey started! Customer has been notified."

**Events Triggered:**
- `OrderInTransit` - Broadcasts to customer, assigned staff, and admins

---

### Mark Delivered (Staff)
**POST** `/orders/{order}/mark-delivered`

Staff marks order as delivered.

**Authentication:** Required (staff role)

**Request Body:**
```json
{}
```

**Response (JSON):**
```json
{
    "success": true,
    "message": "Order marked as delivered"
}
```

**Events Triggered:**
- `OrderDelivered` - Broadcasts to customer, assigned staff, and admins
- Staff profile status updated to 'available'

---

### Confirm Delivery (Customer)
**POST** `/orders/{order}/customer-confirm-delivery`

Customer confirms receiving their order.

**Authentication:** Required (customer role)

**Request Body:**
```json
{}
```

**Response (JSON):**
```json
{
    "success": true,
    "message": "Thank you for confirming delivery!"
}
```

**Events Triggered:**
- `OrderDelivered` - Broadcasts to all stakeholders

---

### Toggle Staff Availability
**POST** `/staff/toggle-availability`

Staff changes their availability status.

**Authentication:** Required (staff role)

**Request Body:**
```json
{
    "status": "available"  // or "unavailable"
}
```

**Response (JSON):**
```json
{
    "success": true,
    "status": "available"
}
```

---

### Cancel Order (Admin)
**DELETE** `/orders/{order}`

Admin cancels an order.

**Authentication:** Required (admin role)

**Response:**
- Redirect with success message
- Assigned staff (if any) marked as available

---

## Broadcasting Events

### OrderAssigned Event

**Event Class:** `App\Events\OrderAssigned`

**Channels:**
- `private-orders.customer.{customerId}`
- `private-orders.staff.{staffId}`
- `private-orders.admin`
- `private-orders.{orderId}`

**Broadcast Name:** `order.assigned`

**Payload:**
```json
{
    "orderId": 42,
    "customerId": 5,
    "staffId": 3,
    "staffName": "John Doe",
    "message": "Order #42 has been assigned to John Doe",
    "status": "assigned",
    "timestamp": "14:30:45"
}
```

**Listening (JavaScript):**
```javascript
channel.bind('order.assigned', (data) => {
    console.log(`Order ${data.orderId} assigned to ${data.staffName}`);
});
```

---

### OrderInTransit Event

**Event Class:** `App\Events\OrderInTransit`

**Channels:** Same as OrderAssigned

**Broadcast Name:** `order.in_transit`

**Payload:**
```json
{
    "orderId": 42,
    "customerId": 5,
    "staffId": 3,
    "staffName": "John Doe",
    "message": "Order #42 is on the way with John Doe",
    "status": "in_transit",
    "timestamp": "14:35:22"
}
```

---

### OrderDelivered Event

**Event Class:** `App\Events\OrderDelivered`

**Channels:** Same as OrderAssigned

**Broadcast Name:** `order.delivered`

**Payload:**
```json
{
    "orderId": 42,
    "customerId": 5,
    "staffId": 3,
    "staffName": "John Doe",
    "message": "Order #42 has been delivered! Please confirm receipt.",
    "status": "delivered",
    "timestamp": "14:40:15"
}
```

---

## Error Responses

### Validation Error (400)
```json
{
    "success": false,
    "message": "No items in order"
}
```

### Unauthorized (403)
```json
{
    "success": false,
    "message": "Unauthorized"
}
```

### Not Found (404)
```json
{
    "success": false,
    "message": "Resource not found"
}
```

---

## Order Status Flow

```
pending
  ↓
assigned (staff assigned, notification sent)
  ↓
in_transit (delivery started)
  ↓
delivered (order completed)

OR at any point:
cancelled (admin cancels)
```

---

## Channel Authorization

Authorization rules in `routes/channels.php`:

```php
// Customer can only access their own orders
Broadcast::private('orders.customer.{customerId}', function ($user, $customerId) {
    return $user->id === (int) $customerId && $user->role === 'customer';
});

// Staff can only access their assigned orders
Broadcast::private('orders.staff.{staffId}', function ($user, $staffId) {
    return $user->id === (int) $staffId && $user->role === 'staff';
});

// Only admins can access admin channel
Broadcast::private('orders.admin', function ($user) {
    return $user->role === 'admin';
});
```

---

## Integration Example

### Server-side (Laravel):

```php
// In OrderController
public function store(Request $request)
{
    $order = Order::create([...]);
    
    if (app(StaffAssignmentService::class)->assign($order)) {
        // Broadcast event - automatically sends to all subscribed users
        broadcast(new OrderAssigned($order));
    }
    
    return response()->json(['success' => true, 'order_id' => $order->id]);
}
```

### Client-side (JavaScript):

```javascript
// Initialize Pusher
PusherNotifications.init('PUSHER_KEY', 'mt1');

// Subscribe to notifications
PusherNotifications.subscribeToCustomerOrders(customerId);

// Events are automatically received and displayed as toasts
```

---

## Database Models

### Order
- `id` - Primary key
- `user_id` - FK to users (customer)
- `delivery_address` - Delivery location
- `total_amount` - Order total
- `status` - pending, assigned, in_transit, delivered, cancelled
- `created_at`, `updated_at`

### OrderItem
- `id` - Primary key
- `order_id` - FK to orders
- `product_id` - FK to products
- `quantity` - Number of items
- `price` - Unit price

### Assignment
- `id` - Primary key
- `order_id` - FK to orders
- `staff_id` - FK to users (staff)

### StaffProfile
- `id` - Primary key
- `user_id` - FK to users (staff)
- `status` - available, assigned, in_transit

---

## Performance Considerations

1. **Order Assignment:** Uses database transactions with row locking to prevent duplicate assignments
2. **Broadcasting:** Async with queued jobs (configurable in `config/queue.php`)
3. **Channel Authorization:** Cached with Laravel's built-in caching

---

## Testing

### Manual Testing with Postman:

1. Create order endpoint: POST `http://localhost:8000/orders`
2. Check admin dashboard for new order
3. Monitor browser console for real-time updates
4. Test staff availability toggle

### Automated Tests:

```bash
php artisan test
```

---

## Debugging

Enable Pusher debug mode:

```javascript
// In browser console
Pusher.logToConsole = true;
```

Check server logs:
```bash
tail -f storage/logs/laravel.log
```

---

## Rate Limiting

Currently no rate limiting on endpoints. Add if needed:

```php
Route::post('/orders', [OrderController::class, 'store'])
    ->middleware(['auth', 'throttle:60,1']) // 60 per minute
    ->name('orders.store');
```
