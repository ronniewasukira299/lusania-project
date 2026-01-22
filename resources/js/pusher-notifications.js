// Pusher Real-time Notifications Utility
// This file provides functions for setting up real-time notifications using Pusher

window.PusherNotifications = {
    initialized: false,
    pusher: null,

    /**
     * Initialize Pusher and set up listeners
     */
    init: function(pusherKey, cluster) {
        if (this.initialized) return;

        if (!pusherKey || pusherKey === 'test_key') {
            console.warn('Pusher not properly configured. Using fallback notifications.');
            this.useFallback = true;
            return;
        }

        this.pusher = new Pusher(pusherKey, {
            cluster: cluster || 'mt1',
            encrypted: true,
            activityTimeout: 60000,
        });

        this.initialized = true;
        console.log('✓ Pusher notifications initialized');
    },

    /**
     * Subscribe to customer notifications
     */
    subscribeToCustomerOrders: function(customerId) {
        if (!this.initialized) return;

        const channel = this.pusher.subscribe(`private-orders.customer.${customerId}`);

        // Order assigned notification
        channel.bind('order.assigned', (data) => {
            this.showNotification(
                '📦 Order Assigned',
                data.message,
                'info',
                () => location.reload()
            );
        });

        // Order in transit notification
        channel.bind('order.in_transit', (data) => {
            this.showNotification(
                '🚚 On The Way',
                data.message,
                'info',
                () => location.reload()
            );
        });

        // Order delivered notification
        channel.bind('order.delivered', (data) => {
            this.showNotification(
                '✅ Delivery Ready',
                data.message,
                'success',
                () => location.reload()
            );
        });
    },

    /**
     * Subscribe to staff order notifications
     */
    subscribeToStaffOrders: function(staffId) {
        if (!this.initialized) return;

        const channel = this.pusher.subscribe(`private-orders.staff.${staffId}`);

        // New order assigned to staff
        channel.bind('order.assigned', (data) => {
            this.showNotification(
                '📦 New Order Assigned',
                `Order #${data.orderId} assigned. Customer: ${data.message}`,
                'info',
                () => location.reload()
            );
        });

        // Confirm delivery notification
        channel.bind('order.delivered', (data) => {
            this.showNotification(
                '✅ Delivery Confirmed',
                data.message,
                'success',
                () => location.reload()
            );
        });
    },

    /**
     * Subscribe to admin notifications
     */
    subscribeToAdminOrders: function() {
        if (!this.initialized) return;

        const channel = this.pusher.subscribe('private-orders.admin');

        // New order in system
        channel.bind('order.assigned', (data) => {
            this.showNotification(
                '📦 New Order',
                `Order #${data.orderId} assigned to ${data.staffName}`,
                'info'
            );
        });

        // Order status updates
        channel.bind('order.in_transit', (data) => {
            this.showNotification(
                '🚚 Order In Transit',
                `Order #${data.orderId} started delivery`,
                'info'
            );
        });

        channel.bind('order.delivered', (data) => {
            this.showNotification(
                '✅ Order Completed',
                `Order #${data.orderId} delivered`,
                'success'
            );
        });
    },

    /**
     * Show toast notification
     */
    showNotification: function(title, message, type = 'info', callback = null) {
        // Create notification container if it doesn't exist
        if (!document.getElementById('notification-container')) {
            const container = document.createElement('div');
            container.id = 'notification-container';
            container.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                z-index: 9999;
                max-width: 400px;
            `;
            document.body.appendChild(container);
        }

        const container = document.getElementById('notification-container');

        // Create notification element
        const notification = document.createElement('div');
        const bgColor = type === 'success' ? '#28a745' : (type === 'error' ? '#dc3545' : '#17a2b8');
        
        notification.style.cssText = `
            background: ${bgColor};
            color: white;
            padding: 16px;
            border-radius: 8px;
            margin-bottom: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            cursor: pointer;
            transition: all 0.3s ease;
            animation: slideIn 0.3s ease;
        `;

        notification.innerHTML = `
            <div style="font-weight: bold; margin-bottom: 4px;">${title}</div>
            <div style="font-size: 14px;">${message}</div>
        `;

        notification.addEventListener('click', () => {
            notification.style.animation = 'slideOut 0.3s ease';
            setTimeout(() => notification.remove(), 300);
            if (callback) callback();
        });

        container.appendChild(notification);

        // Auto remove after 8 seconds
        setTimeout(() => {
            if (notification.parentElement) {
                notification.style.animation = 'slideOut 0.3s ease';
                setTimeout(() => notification.remove(), 300);
            }
        }, 8000);
    },

    /**
     * Disconnect from Pusher
     */
    disconnect: function() {
        if (this.pusher) {
            this.pusher.disconnect();
            this.initialized = false;
        }
    }
};

// Add animation styles
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from {
            transform: translateX(400px);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(400px);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);
