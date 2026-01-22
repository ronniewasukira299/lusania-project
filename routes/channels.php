<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\Order;
use App\Models\User;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

/*
|--------------------------------------------------------------------------
| Order Channels
|--------------------------------------------------------------------------
*/

// Private channel for specific customer to receive order updates
Broadcast::private('orders.customer.{customerId}', function ($user, $customerId) {
    return $user->id === (int) $customerId && $user->role === 'customer';
});

// Private channel for specific staff member to receive assigned orders
Broadcast::private('orders.staff.{staffId}', function ($user, $staffId) {
    return $user->id === (int) $staffId && $user->role === 'staff';
});

// Admin channel to broadcast all order updates
Broadcast::private('orders.admin', function ($user) {
    return $user->role === 'admin';
});

// Public channel for order status updates (can be used for webhooks)
Broadcast::channel('orders.{orderId}', function ($user, $orderId) {
    $order = Order::find($orderId);
    
    if (!$order) {
        return false;
    }
    
    // Check if user is authorized to subscribe
    $isCustomer = $user->id === $order->user_id;
    $isAssignedStaff = $order->assignment && $user->id === $order->assignment->staff_id;
    $isAdmin = $user->role === 'admin';
    
    if ($isCustomer || $isAssignedStaff || $isAdmin) {
        return ['id' => $user->id, 'name' => $user->name];
    }
    
    return false;
});
