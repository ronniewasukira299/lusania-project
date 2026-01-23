# Testing Notifications and Order Lifecycle

## Current Setup Status
- ✅ Database: 3 products loaded (Classic, Spicy, Family Pack Lusaniya - each 50,000 UGX)
- ✅ Server: Running on http://localhost:8000
- ✅ Products Page: http://localhost:8000/products
- ✅ Broadcasting: Pusher infrastructure configured (private channels)
- ⚠️ Pusher Credentials: Not yet configured (notifications will log but not send)

## Complete Order Lifecycle Testing (Step-by-Step)

### PHASE 1: SET UP TEST ACCOUNTS
Navigate to http://localhost:8000 and create these test accounts:

**Account 1 - Customer**
- Name: John Customer
- Email: john@example.com
- Password: password123
- Phone: +256 701234567
- Address: Plot 123, Kampala

**Account 2 - Staff Member**
- Email: staff@example.com (use registration at /secret-staff-register)
- Password: password123
- Name: Sarah Staff
- Phone: +256 702345678

**Account 3 - Admin**
- Email: admin@example.com (use registration at /secret-admin-register)
- Password: password123
- Name: Admin User

### PHASE 2: TEST CUSTOMER FLOW
**As: John Customer (john@example.com)**

1. **Add Products to Cart**
   - Go to /products
   - Add "Classic Chicken Lusaniya" qty 2 to cart
   - Add "Family Pack Lusaniya" qty 1 to cart
   - Verify cart shows 3 items

2. **Proceed to Checkout**
   - Click "View Cart"
   - Verify items and totals (2×50,000 + 1×50,000 = 150,000 UGX)
   - Click "Proceed to Checkout"

3. **Place Order**
   - Verify customer info pre-filled (email, phone, address)
   - Select delivery method: "Deliver to Address" or "WhatsApp Me Link"
   - Click "Place Order"
   - **EXPECTED**: 
     - Order created with ID
     - Redirect to success page
     - Order appears in customer's order history

**Navigate to /orders (My Orders)**
   - Verify order shows status: "pending"
   - Note the Order ID for next phase

### PHASE 3: TEST STAFF FLOW (Real-Time Notifications)
**Open two browser tabs: Tab A (Staff) and Tab B (Customer)**

**Tab A - Staff Login**
- Go to http://localhost:8000/staff
- Login with staff@example.com / password123
- Navigate to Staff Dashboard (/staff)

**Tab B - Keep Customer Order Page Open**
- Already on /orders as John Customer
- Keep page open to see notifications

### PHASE 4: TRIGGER NOTIFICATIONS - ORDER ASSIGNMENT
**In Tab A (Staff Dashboard):**

1. Find the customer's pending order in "Assigned Orders"
2. Review order details:
   - Customer name, phone, address
   - Order items and total
3. If order auto-assigned, proceed to step 4

**EXPECTED IN TAB B (Customer):**
- ✨ Toast notification slides in from top-right
- Message: "Order #{id} assigned to staff member {name}"
- Toast auto-dismisses after 8 seconds
- Order status changes to "assigned"

### PHASE 5: TRIGGER NOTIFICATIONS - START DELIVERY
**In Tab A (Staff Dashboard):**

1. In the assigned order, click "Start Delivery"
2. Confirm action

**EXPECTED IN TAB B (Customer):**
- ✨ Toast notification slides in
- Message: "Order #{id} is in transit!"
- Order status changes to "in_transit"
- Map view might show delivery progress (if implemented)

### PHASE 6: TRIGGER NOTIFICATIONS - MARK DELIVERED
**In Tab A (Staff Dashboard):**

1. In the in-transit order, click "Mark as Delivered"
2. Confirm action

**EXPECTED IN TAB B (Customer):**
- ✨ Toast notification slides in
- Message: "Order #{id} has been delivered!"
- Order status changes to "delivered"
- Verification button appears

**In Tab B (Customer):**
1. Click "Confirm Delivery Received"
2. Confirm action

**EXPECTED IN TAB A (Staff):**
- Order status shows "confirmed" or "completed"

### PHASE 7: TEST ADMIN FLOW
**Admin Login**
- Go to http://localhost:8000/admin
- Login with admin@example.com / password123

**Admin Dashboard (/admin)**
- View all orders (including customer's order)
- Verify statistics:
  - Total Orders: 1
  - Completed Orders: Should show completed count
  - Total Revenue: 150,000 UGX
- View orders table with all details

### PHASE 8: NOTIFICATION SYSTEM VERIFICATION

**Check Browser Console (F12)**
- Look for Pusher connection logs:
  - "✓ Connected to Pusher"
  - "✓ Subscribed to customer channel"
  - "✓ Listening for order events"

**Check Network Tab (F12 → Network)**
- Filter by "pusher"
- Should see WebSocket connections establishing

**Notification Logs (If Pusher Configured)**
- Toast notifications should appear for:
  - OrderAssigned event
  - OrderInTransit event
  - OrderDelivered event

## Debugging Checklist

### If Notifications Not Appearing:

1. **Check Pusher Configuration**
   ```bash
   php artisan tinker
   > config('broadcasting.default')
   > config('broadcasting.connections.pusher')
   ```

2. **Verify Channel Authorization**
   - Browser console should show successful channel subscriptions
   - No "403 Forbidden" errors

3. **Check Event Broadcasting**
   - Log file: `storage/logs/laravel.log`
   - Look for: "Broadcasting [OrderAssigned] event"

4. **Test Without Pusher (Fallback)**
   - Orders should still work without notifications
   - No errors should appear
   - Status updates should still occur

### Network Issues:
```bash
# Restart cache/config
php artisan config:clear
php artisan cache:clear
php artisan view:clear

# Restart server
php artisan serve
```

## Expected Notification Toast UI

**Appearance:**
- Slides in from top-right corner
- Dark background with white text
- Green checkmark icon
- Auto-closes after 8 seconds
- Can be clicked to dismiss

**Messages:**
- Assignment: "✓ Order #123 assigned to Sarah Staff"
- In Transit: "✓ Order #123 is in transit!"
- Delivered: "✓ Order #123 has been delivered!"

## Testing Real-Time Without Pusher Credentials

If Pusher credentials not configured:
1. Orders still create successfully ✓
2. Status updates work via page reload ✓
3. Notifications don't send but fall back gracefully ✓
4. Database transactions complete ✓
5. No errors in application ✓

## Performance Metrics to Monitor

During testing, check:
1. Page load time: <2 seconds
2. Order creation time: <1 second
3. Notification display time: <500ms (if Pusher enabled)
4. Server response for status updates: <200ms
5. Database query count: <10 per request

## Success Criteria

✅ All tests pass when:
- Orders create without errors
- Status updates persist in database
- Notifications fire (or gracefully skip if no Pusher)
- All three dashboards display correct data
- No 500 errors in server logs
- No JavaScript console errors

## Next Steps After Testing

1. **Configure Pusher Credentials** (for real-time notifications)
   - Sign up at https://pusher.com (free tier)
   - Add credentials to .env file
   - Run: `php artisan config:cache`

2. **Test with Pusher Enabled**
   - Notifications should now display in real-time
   - Multiple browser tabs should all receive updates

3. **Load Testing**
   - Create multiple orders
   - Verify performance remains good
   - Check database optimization

4. **Deploy to Production**
   - Set correct environment variables
   - Enable HTTPS for security
   - Configure Pusher for production

---

**Test Duration:** ~30-45 minutes for complete cycle
**Browsers Tested:** Chrome, Firefox, Edge (all support WebSockets)
**Devices:** Desktop, Mobile (if testing responsive)
