<div class="page-header">
    <h1><i class="fas fa-receipt"></i> Order History</h1>
    <p>View your past orders and their status</p>
</div>

<div id="orders-container">
    <div class="text-center">Loading...</div>
</div>

<!-- Order Details Modal -->
<div class="modal-overlay" id="order-details-modal">
    <div class="modal" style="max-width: 600px;">
        <div class="modal-header">
            <h3><i class="fas fa-receipt"></i> Order Details</h3>
            <button class="modal-close" onclick="closeModal('order-details-modal')">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body" id="order-details-content">
            <!-- Order details will be loaded here -->
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    loadOrders();
});

async function loadOrders() {
    const result = await apiRequest('get_orders');
    const container = document.getElementById('orders-container');
    
    if (result.success && result. data.length > 0) {
        container.innerHTML = result.data.map(order => `
            <div class="order-card">
                <div class="order-header">
                    <div>
                        <div class="order-id">Order #${order. id}</div>
                        <div class="order-date">${formatDate(order.created_at)}</div>
                    </div>
                    <div>
                        <span class="badge ${getStatusBadge(order.status)}">${order.status}</span>
                    </div>
                </div>
                <div class="order-body">
                    <div class="flex-between">
                        <div>
                            <strong>${order.item_count} item(s)</strong>
                        </div>
                        <div>
                            <strong>Total: $${parseFloat(order.total_amount).toFixed(2)}</strong>
                        </div>
                    </div>
                    <div style="margin-top:  15px;">
                        <button class="btn btn-sm btn-secondary" onclick="viewOrderDetails(${order. id})">
                            <i class="fas fa-eye"></i> View Details
                        </button>
                    </div>
                </div>
            </div>
        `).join('');
    } else {
        container.innerHTML = `
            <div class="empty-state">
                <i class="fas fa-shopping-cart"></i>
                <h3>No Orders Yet</h3>
                <p>You haven't placed any orders yet</p>
                <a href="index. php?page=browse_products" class="btn btn-primary">
                    <i class="fas fa-shopping-bag"></i> Start Shopping
                </a>
            </div>
        `;
    }
}

function getStatusBadge(status) {
    switch (status) {
        case 'completed': return 'badge-success';
        case 'processing': return 'badge-primary';
        case 'pending': return 'badge-warning';
        case 'cancelled': return 'badge-danger';
        default: return 'badge-secondary';
    }
}

async function viewOrderDetails(orderId) {
    const result = await apiRequest('get_order_details', { order_id: orderId });
    
    if (result. success) {
        const items = result.data;
        let itemsHtml = '';
        let total = 0;
        
        items.forEach(item => {
            const subtotal = item.price * item.quantity;
            total += subtotal;
            itemsHtml += `
                <div class="order-item">
                    <div>
                        <strong>${item.product_name}</strong>
                        <br><small>Qty: ${item. quantity} × $${parseFloat(item.price).toFixed(2)}</small>
                    </div>
                    <div><strong>$${subtotal.toFixed(2)}</strong></div>
                </div>
            `;
        });
        
        document.getElementById('order-details-content').innerHTML = `
            <div class="order-items">
                ${itemsHtml}
            </div>
            <div class="order-total">
                <span>Total: </span>
                <span>$${total.toFixed(2)}</span>
            </div>
        `;
        
        openModal('order-details-modal');
    }
}
</script>