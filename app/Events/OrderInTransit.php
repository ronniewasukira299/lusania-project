<?php

namespace App\Events;

use App\Models\Order;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderInTransit implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $order;
    public $staffName;
    public $customerId;

    /**
     * Create a new event instance.
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
        $this->staffName = $order->assignment->staff->name ?? 'Staff';
        $this->customerId = $order->user_id;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            // Notify customer
            new PrivateChannel("orders.customer.{$this->customerId}"),
            // Notify assigned staff
            new PrivateChannel("orders.staff.{$this->order->assignment->staff_id}"),
            // Notify all admins
            new PrivateChannel("orders.admin"),
            // Order-specific channel
            new PrivateChannel("orders.{$this->order->id}"),
        ];
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'orderId' => $this->order->id,
            'customerId' => $this->customerId,
            'staffId' => $this->order->assignment->staff_id,
            'staffName' => $this->staffName,
            'message' => "Order #{$this->order->id} is on the way with {$this->staffName}",
            'status' => 'in_transit',
            'timestamp' => now()->format('H:i:s'),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'order.in_transit';
    }
}
