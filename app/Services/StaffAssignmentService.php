<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Assignemet;
use App\Models\StaffProfile;
use Illuminate\Support\Facades\DB;

class StaffAssignmentService
{
    /**
     * Assign a staff member to the given order.
     */
    
    public function assign(Order $order): bool
    {    
        return DB::transaction(function () use ($order) {
           //Quick early return if order is not pending
            if ($order->status !== 'pending') {
                return false;
            }
          // Find the first available staff member and **LOCK** for update
          //This prevents another request from assigning the same staff
            $staffProfile = StaffProfile::where('status', 'available')
                -> lockForUpdate()
                ->first();
            
            if (!$staffProfile) {
                return false; // No available staff found
            }    

            Assignment::create([
                'order_id' => $order->id,
                'staff_id' => $staffProfile->user_id,
            ]);

            $staffProfile->update(['status' => 'assigned']);
            $order->update(['status' => 'assigned']);
            return true;
        });
    }
}
