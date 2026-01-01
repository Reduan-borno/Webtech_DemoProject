<div class="page-header">
    <h1><i class="fas fa-tachometer-alt"></i> My Dashboard</h1>
    <p>Welcome back, <? = htmlspecialchars($_SESSION['full_name'] ??  'Customer') ?>!</p>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon primary">
            <i class="fas fa-shopping-bag"></i>
        </div>
        <div class="stat-details">
            <h3 id="stat-orders">0</h3>
            <p>My Orders</p>
        </div>
    </div>
    
    <div class="stat-card danger">
        <div class="stat-icon danger">
            <i class="fas fa-heart"></i>
        </div>
        <div class="stat-details">
            <h3 id="stat-wishlist">0</h3>
            <p>Wishlist Items</p>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="card mb-20">
    <div class="card-header">
        <h2><i class="fas fa-bolt"></i> Quick Actions</h2>
    </div>
    <div style="display: flex; gap: 15px; flex-wrap: wrap; padding: 10px 0;">
        <a href="index.php?page=browse_products" class="btn btn-primary">
            <i class="fas fa-shopping-bag"></i> Browse Products
        </a>
        <a href="index.php? page=wishlist" class="btn btn-danger">
            <i class="fas fa-heart"></i> View Wishlist
        </a>
        <a href="index.php?page=orders" class="btn btn-secondary">
            <i class="fas fa-receipt"></i> Order History
        </a>
        <a href="index.php?page=profile" class="btn btn-info">
            <i class="fas fa-user"></i> Edit Profile
        </a>
    </div>
</div>

<!-- Recent Orders -->
<div class="card mb-20">
    <div class="card-header">
        <h2><i class="fas fa-clock"></i> Recent Orders</h2>
        <a href="index.php?page=orders" class="btn btn-sm btn-secondary">View All</a>
    </div>
    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Date</th>
                    <th>Items</th>
                    <th>Total</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody id="recent-orders">
                <tr>
                    <td colspan="5" class="text-center">Loading...</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Featured Products -->
<div class="card">
    <div class="card-header">
        <h2><i class="fas fa-fire"></i> Featured Products</h2>
        <a href="index.php?page=browse_products" class="btn btn-sm btn-secondary">View All</a>
    </div>
    <div class="products-grid" id="featured-products" style="padding: 20px 0;">
        <div class="text-center">Loading...</div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    loadCustomerDashboard();
    loadRecentOrders();
    loadFeaturedProducts();
});

async function loadCustomerDashboard() {
    const result = await apiRequest('get_customer_stats');
    if (result.success) {
        document.getElementById('stat-orders').textContent = result. data.total_orders || 0;
        document.getElementById('stat-wishlist').textContent = result.data.wishlist_items || 0;
    }
}

async function loadRecentOrders() {
    const result = await apiRequest('get_orders');
    const tbody = document.getElementById('recent-orders');
    
    if (result.success && result.data.length > 0) {
        const recentOrders = result.data.slice(0, 5);
        tbody.innerHTML = recentOrders.map(order => `
            <tr>
                <td><strong>#${order.id}</strong></td>
                <td>${formatDate(order.created_at)}</td>
                <td>${order.item_count} items</td>
                <td><strong>$${parseFloat(order.total_amount).toFixed(2)}</strong></td>
                <td>
                    <span class="badge ${getStatusBadge(order.status)}">
                        ${order.status}
                    </span>
                </td>
            </tr>
        `).join('');
    } else {
        tbody.innerHTML = '<tr><td colspan="5" class="text-center">No orders yet.  <a href="index.php?page=browse_products">Start shopping! </a></td></tr>';
    }
}

async function loadFeaturedProducts() {
    const result = await apiRequest('get_products');
    const container = document.getElementById('featured-products');
    
    if (result.success && result.data.length > 0) {
        const featured = result.data. slice(0, 4);
        container. innerHTML = featured.map(product => createProductCard(product)).join('');
    } else {
        container.innerHTML = '<div class="empty-state"><p>No products available</p></div>';
    }
}

function getStatusBadge(status) {
    switch (status) {
        case 'completed':  return 'badge-success';
        case 'processing': return 'badge-primary';
        case 'pending': return 'badge-warning';
        case 'cancelled': return 'badge-danger';
        default: return 'badge-secondary';
    }
}

function createProductCard(product) {
    const hasOffer = product.offer && product.offer.discount_percentage;
    const discountedPrice = hasOffer ? (product.price * (1 - product.offer.discount_percentage / 100)).toFixed(2) : null;
    
    return `
        <div class="product-card">
            <div class="product-image">
                <i class="fas fa-box"></i>
                ${hasOffer ? `<span class="product-offer">${product.offer. discount_percentage}% OFF</span>` : ''}
            </div>
            <div class="product-content">
                <div class="product-category">${product.category_name || 'General'}</div>
                <h3 class="product-title">${product.name}</h3>
                <div class="product-price">
                    ${hasOffer ? `
                        <span class="current">$${discountedPrice}</span>
                        <span class="original">$${parseFloat(product.price).toFixed(2)}</span>
                    ` : `
                        <span class="current">$${parseFloat(product.price).toFixed(2)}</span>
                    `}
                </div>
                <div class="product-actions">
                    <a href="index.php?page=browse_products" class="btn btn-primary btn-sm">View Details</a>
                </div>
            </div>
        </div>
    `;
}
</script>