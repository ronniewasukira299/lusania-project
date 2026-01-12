<?php

namespace App\Services;

use App\Models\Order;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;

class OrderLifecycleService
{
   public function startJourney(Order $order, User $staff)
    {
        if (!$staff->isStaff()) {
            throw new AuthorizationException();
        }

        if ($order->status !== 'assigned') {
            throw new \Exception('Order not ready for transit.');
        }

        if ($order->assignment->staff_id !== $staff->id) {
            throw new AuthorizationException();
        }

        $order->update(['status' => 'in_transit']);
        $staff->staffProfile->update(['status' => 'in_transit']);
    }

    public function confirmDelivery(Order $order, User $customer)
    {
        if ($order->user_id !== $customer->id) {
            throw new AuthorizationException();
        }

        if ($order->status !== 'in_transit') {
            throw new \Exception('Order not in transit.');
        }

        $order->update(['status' => 'delivered']);

        $staffProfile = $order->assignment->staff->staffProfile;
        $staffProfile->update(['status' => 'available']);
    }
}
