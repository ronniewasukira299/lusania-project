# Pusher Real-Time Notifications Setup Guide

This document explains how to set up and use the Pusher real-time notification system for your Caleb's Chicken Lusania application.

## Overview

The system uses Pusher to broadcast real-time events when:
- An order is assigned to staff
- Staff starts delivery (order in transit)
- Order is delivered
- Customer confirms delivery

## Prerequisites

1. Pusher account (sign up at https://pusher.com)
2. Pusher PHP library (already installed via composer)
3. Pusher JavaScript library (loaded via CDN in layout)

## Configuration Steps

### Step 1: Get Pusher Credentials

1. Go to https://dashboard.pusher.com
2. Create a new app or select existing one
3. Copy your credentials:
   - App ID
   - App Key
   - App Secret
   - App Cluster (e.g., mt1, us2, eu)

### Step 2: Update Environment File

Update your `.env` file with Pusher credentials:

```
BROADCAST_DRIVER=pusher
BROADCAST_CONNECTION=pusher

PUSHER_APP_ID=YOUR_APP_ID
PUSHER_APP_KEY=YOUR_APP_KEY
PUSHER_APP_SECRET=YOUR_APP_SECRET
PUSHER_APP_CLUSTER=mt1
PUSHER_HOST=api-mt1.pusher.com
PUSHER_PORT=443
PUSHER_SCHEME=https
```

Replace `YOUR_APP_ID`, `YOUR_APP_KEY`, and `YOUR_APP_SECRET` with actual credentials from Pusher dashboard.

### Step 3: Clear Configuration Cache

```bash
php artisan config:cache
php artisan optimize:clear
```

## How It Works

### Broadcasting Channels

Three private channels are used:

1. **`orders.customer.{customerId}`** - Customer notifications
   - Order assigned
   - Order in transit
   - Order delivered

2. **`orders.staff.{staffId}`** - Staff notifications
   - New order assigned
   - Delivery confirmed

3. **`orders.admin`** - Admin notifications
   - All order updates
   - Staff assignments

### Events

#### OrderAssigned
Triggered when an order is automatically assigned to available staff.
- Notifies: Customer, assigned staff, all admins
- Event name: `order.assigned`

#### OrderInTransit
Triggered when staff starts delivery.
- Notifies: Customer, assigned staff, all admins
- Event name: `order.in_transit`

#### OrderDelivered
Triggered when:
- Staff marks order as delivered, OR
- Customer confirms delivery receipt
- Notifies: Customer, assigned staff, all admins
- Event name: `order.delivered`

## JavaScript Integration

### Notification Display

Notifications appear as toast notifications in the top-right corner with auto-hide after 8 seconds.

Toast includes:
- Title (Order status change)
- Message (Order details)
- Color (Green for success, Blue for info, Red for errors)
- Click to dismiss

### Usage in Views

The app layout automatically initializes Pusher notifications. To use in other views:

```javascript
// Show notification
PusherNotifications.showNotification(
    'Title',
    'Message text',
    'success', // or 'info', 'error'
    () => { /* Optional callback */ }
);

// Subscribe to customer orders
PusherNotifications.subscribeToCustomerOrders(customerId);

// Subscribe to staff orders
PusherNotifications.subscribeToStaffOrders(staffId);

// Subscribe to admin notifications
PusherNotifications.subscribeToAdminOrders();
```

## Testing Without Pusher

If you don't have Pusher configured yet, the app will:
1. Show a console warning
2. Set `useFallback = true`
3. Still work with AJAX/form submissions
4. Not show real-time notifications

Once you add Pusher credentials, real-time notifications will work automatically.

## File Structure

```
app/
  Events/
    OrderAssigned.php      # Event when order assigned to staff
    OrderInTransit.php     # Event when delivery starts
    OrderDelivered.php     # Event when delivery complete

config/
  broadcasting.php         # Broadcasting driver configuration

resources/
  js/
    pusher-notifications.js # Notification utility

routes/
  channels.php            # Channel authorization

.env                      # Pusher credentials
```

## Broadcasting Authorization

Channels are protected with authorization rules in `routes/channels.php`:

- Customers can only subscribe to their own order updates
- Staff can only subscribe to their assigned orders
- Admins can subscribe to all order updates

## Troubleshooting

### Notifications not appearing?

1. **Check Pusher credentials** in `.env` file
2. **Verify BROADCAST_DRIVER** is set to `pusher`
3. **Check browser console** for errors
4. **Verify channel names** match between backend and frontend

### Real-time updates not working?

1. Ensure app is running with: `php artisan serve`
2. Check that WebSocket is not blocked by firewall/proxy
3. Verify Pusher app is active in dashboard

### Testing notifications locally?

Use browser DevTools Network tab to see Pusher connections:
- Filter by `pusher` to see WebSocket connections
- Check for `private-` channel subscriptions

## Security Considerations

1. **Private channels** are protected by authentication
2. **CSRF tokens** are included in all requests
3. **User role** validation prevents unauthorized access
4. **Channel authorization** ensures users only receive their own notifications

## Future Enhancements

- SMS notifications for critical events
- Email notifications with order summary
- Notification history/logs
- Sound alerts for new orders (staff)
- Push notifications for mobile app

## Reference

- Pusher Documentation: https://pusher.com/docs
- Laravel Broadcasting: https://laravel.com/docs/broadcasting
- Channels.js (Pusher JS library): https://js.pusher.com
