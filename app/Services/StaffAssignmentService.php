<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Assignment;
use App\Models\StaffProfile;
use Illuminate\Support\Facades\DB;

class StaffAssignmentService
{
    /**
     * Assign a random available staff member to the given order.
     * Works automatically regardless of whether staff tab is open.
     * Handles multiple available staff by randomly selecting one.
     */
    
    public function assign(Order $order): bool
    {    
        return DB::transaction(function () use ($order) {
            // Early return if order is not pending
            if ($order->status !== 'pending') {
                return false;
            }
            
            // Find all available staff members with pessimistic locking
            // This prevents race conditions when multiple orders arrive simultaneously
            $availableStaff = StaffProfile::where('status', 'available')
                ->lockForUpdate()
                ->get();
            
            if ($availableStaff->isEmpty()) {
                // No available staff found - order remains pending
                return false;
            }
            
            // If multiple staff available, pick one randomly
            // This distributes orders fairly among all available staff
            $selectedStaffProfile = $availableStaff->random();
            
            // Create assignment record
            Assignment::create([
                'order_id' => $order->id,
                'staff_id' => $selectedStaffProfile->user_id,
            ]);
            
            // Update staff status to 'assigned'
            $selectedStaffProfile->update(['status' => 'assigned']);
            
            // Update order status to 'assigned'
            $order->update(['status' => 'assigned']);
            
            return true;
        });
    }
}
