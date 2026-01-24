/**
 * Pusher Real-Time Notifications System
 * Handles all real-time order updates and notifications
 */

const PusherNotifications = {
    pusher: null,
    channels: {},

    /**
     * Initialize Pusher with credentials
     */
    init(appKey, cluster) {
        if (this.pusher) return; // Already initialized

        this.pusher = new Pusher(appKey, {
            cluster: cluster,
            encrypted: true
        });

        console.log('✅ Pusher initialized successfully', { appKey: appKey.substring(0, 8) + '...', cluster });
        return this.pusher;
    },

    /**
     * Subscribe customer to their order updates
     */
    subscribeToCustomerOrders(customerId) {
        if (!this.pusher) {
            console.error('❌ Pusher not initialized');
            return;
        }

        const channelName = `private-orders.customer.${customerId}`;
        const channel = this.pusher.subscribe(channelName);

        channel.bind('pusher:subscription_succeeded', () => {
            console.log(`✅ Subscribed to customer orders: ${channelName}`);
        });

        channel.bind('pusher:subscription_error', (status) => {
            console.error(`❌ Failed to subscribe to ${channelName}:`, status);
        });

        // Listen for order status updates
        channel.bind('order.assigned', (data) => {
            console.log('📦 Order assigned:', data);
            this.showNotification('Order Assigned', `Your order #${data.order_id} has been assigned to delivery personnel`);
            if (window.updateOrderUI) {
                window.updateOrderUI(data.order_id, 'assigned', data);
            }
        });

        channel.bind('order.in_transit', (data) => {
            console.log('🚚 Order in transit:', data);
            this.showNotification('On The Way', `Order #${data.order_id} is on its way to you`);
            if (window.updateOrderUI) {
                window.updateOrderUI(data.order_id, 'in_transit', data);
            }
        });

        channel.bind('order.delivered', (data) => {
            console.log('✅ Order delivered:', data);
            this.showNotification('Order Delivered', `Order #${data.order_id} has been delivered!`);
            if (window.updateOrderUI) {
                window.updateOrderUI(data.order_id, 'delivered', data);
            }
        });

        this.channels[channelName] = channel;
    },

    /**
     * Subscribe staff to new order assignments
     */
    subscribeToStaffOrders(staffId) {
        if (!this.pusher) {
            console.error('❌ Pusher not initialized');
            return;
        }

        const channelName = `private-orders.staff.${staffId}`;
        const channel = this.pusher.subscribe(channelName);

        channel.bind('pusher:subscription_succeeded', () => {
            console.log(`✅ Subscribed to staff orders: ${channelName}`);
        });

        channel.bind('pusher:subscription_error', (status) => {
            console.error(`❌ Failed to subscribe to ${channelName}:`, status);
        });

        // Listen for new assignments
        channel.bind('order.assigned', (data) => {
            console.log('📦 New order assigned to you:', data);
            this.showNotification('New Order!', `Order #${data.order_id} assigned to you. Tap to view details.`);
        });

        this.channels[channelName] = channel;
    },

    /**
     * Subscribe admin to all order updates
     */
    subscribeToAdminOrders() {
        if (!this.pusher) {
            console.error('❌ Pusher not initialized');
            return;
        }

        const channelName = 'private-orders.admin';
        const channel = this.pusher.subscribe(channelName);

        channel.bind('pusher:subscription_succeeded', () => {
            console.log(`✅ Subscribed to admin orders: ${channelName}`);
        });

        channel.bind('pusher:subscription_error', (status) => {
            console.error(`❌ Failed to subscribe to ${channelName}:`, status);
        });

        // Listen for all order events
        channel.bind('order.created', (data) => {
            console.log('📦 New order created:', data);
            this.showNotification('New Order', `Order #${data.order_id} from customer`);
        });

        channel.bind('order.assigned', (data) => {
            console.log('👤 Order assigned:', data);
        });

        channel.bind('order.in_transit', (data) => {
            console.log('🚚 Order in transit:', data);
        });

        channel.bind('order.delivered', (data) => {
            console.log('✅ Order delivered:', data);
        });

        this.channels[channelName] = channel;
    },

    /**
     * Subscribe to a specific order's updates (real-time tracking)
     */
    subscribeToOrderUpdates(orderId) {
        if (!this.pusher) {
            console.error('❌ Pusher not initialized');
            return;
        }

        const channelName = `private-orders.${orderId}`;
        const channel = this.pusher.subscribe(channelName);

        channel.bind('pusher:subscription_succeeded', () => {
            console.log(`✅ Subscribed to order updates: ${channelName}`);
        });

        channel.bind('pusher:subscription_error', (status) => {
            console.error(`❌ Failed to subscribe to ${channelName}:`, status);
        });

        // Update UI in real-time
        channel.bind('order.assigned', (data) => {
            if (window.updateOrderUI) {
                window.updateOrderUI(orderId, 'assigned', data);
            }
        });

        channel.bind('order.in_transit', (data) => {
            if (window.updateOrderUI) {
                window.updateOrderUI(orderId, 'in_transit', data);
            }
        });

        channel.bind('order.delivered', (data) => {
            if (window.updateOrderUI) {
                window.updateOrderUI(orderId, 'delivered', data);
            }
        });

        this.channels[channelName] = channel;
    },

    /**
     * Show a browser notification
     */
    showNotification(title, message) {
        // Browser notification (requires user permission)
        if ('Notification' in window && Notification.permission === 'granted') {
            new Notification(title, {
                body: message,
                icon: '/images/st.jpeg',
                badge: '/images/st.jpeg'
            });
        }

        // Toast notification (visual feedback)
        this.showToast(title, message);
    },

    /**
     * Show an on-screen toast notification
     */
    showToast(title, message) {
        const toastHTML = `
            <div class="toast position-fixed bottom-0 end-0 m-3" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="toast-header bg-success text-white">
                    <strong class="me-auto">${title}</strong>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
                <div class="toast-body">
                    ${message}
                </div>
            </div>
        `;

        const container = document.getElementById('toast-container') || this.createToastContainer();
        container.insertAdjacentHTML('beforeend', toastHTML);

        // Bootstrap toast
        const toastElement = container.lastElementChild;
        const toast = new bootstrap.Toast(toastElement);
        toast.show();

        // Remove element after toast hides
        toastElement.addEventListener('hidden.bs.toast', () => {
            toastElement.remove();
        });
    },

    /**
     * Create toast container if it doesn't exist
     */
    createToastContainer() {
        let container = document.getElementById('toast-container');
        if (!container) {
            container = document.createElement('div');
            container.id = 'toast-container';
            document.body.appendChild(container);
        }
        return container;
    },

    /**
     * Unsubscribe from a channel
     */
    unsubscribe(channelName) {
        if (this.channels[channelName]) {
            this.pusher.unsubscribe(channelName);
            delete this.channels[channelName];
            console.log(`✅ Unsubscribed from ${channelName}`);
        }
    },

    /**
     * Get all active channels
     */
    getActiveChannels() {
        return Object.keys(this.channels);
    }
};

// Request browser notification permission on page load
if ('Notification' in window && Notification.permission === 'default') {
    Notification.requestPermission().then(permission => {
        if (permission === 'granted') {
            console.log('✅ Browser notifications enabled');
        }
    });
}

console.log('✅ PusherNotifications system loaded');
