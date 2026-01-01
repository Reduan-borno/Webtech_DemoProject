<div class="page-header">
    <h1><i class="fas fa-tachometer-alt"></i> Employee Dashboard</h1>
    <p>Welcome back!  Manage your inventory and customers. </p>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon primary">
            <i class="fas fa-box"></i>
        </div>
        <div class="stat-details">
            <h3 id="stat-products">0</h3>
            <p>Total Products</p>
        </div>
    </div>
    
    <div class="stat-card success">
        <div class="stat-icon success">
            <i class="fas fa-users"></i>
        </div>
        <div class="stat-details">
            <h3 id="stat-customers">0</h3>
            <p>Total Customers</p>
        </div>
    </div>
    
    <div class="stat-card warning">
        <div class="stat-icon warning">
            <i class="fas fa-exclamation-triangle"></i>
        </div>
        <div class="stat-details">
            <h3 id="stat-lowstock">0</h3>
            <p>Low Stock Items</p>
        </div>
    </div>
    
    <div class="stat-card info">
        <div class="stat-icon info">
            <i class="fas fa-percent"></i>
        </div>
        <div class="stat-details">
            <h3 id="stat-offers">0</h3>
            <p>Active Offers</p>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="card mb-20">
    <div class="card-header">
        <h2><i class="fas fa-bolt"></i> Quick Actions</h2>
    </div>
    <div style="display: flex; gap: 15px; flex-wrap: wrap; padding: 10px 0;">
        <a href="index.php?page=products" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add Product
        </a>
        <a href="index. php?page=inventory" class="btn btn-success">
            <i class="fas fa-arrow-down"></i> Stock In
        </a>
        <a href="index.php? page=employee_customers" class="btn btn-secondary">
            <i class="fas fa-user-plus"></i> Add Customer
        </a>
        <a href="index.php? page=offers" class="btn btn-warning">
            <i class="fas fa-tags"></i> Create Offer
        </a>
    </div>
</div>

<!-- Low Stock Alert -->
<div class="card">
    <div class="card-header">
        <h2><i class="fas fa-exclamation-circle"></i> Low Stock Alerts</h2>
        <a href="index.php?page=inventory" class="btn btn-sm btn-secondary">View Inventory</a>
    </div>
    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Current Stock</th>
                    <th>Reorder Level</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody id="low-stock-table">
                <tr>
                    <td colspan="4" class="text-center">Loading...</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    loadEmployeeDashboard();
    loadLowStockItems();
});

async function loadEmployeeDashboard() {
    const result = await apiRequest('get_employee_stats');
    if (result.success) {
        document. getElementById('stat-products').textContent = result.data.total_products || 0;
        document.getElementById('stat-customers').textContent = result.data. total_customers || 0;
        document.getElementById('stat-lowstock').textContent = result.data. low_stock_count || 0;
        document.getElementById('stat-offers').textContent = result.data.active_offers || 0;
    }
}

async function loadLowStockItems() {
    const result = await apiRequest('get_low_stock');
    const tbody = document.getElementById('low-stock-table');
    
    if (result.success && result.data.length > 0) {
        tbody.innerHTML = result.data. map(item => `
            <tr>
                <td><strong>${item.product_name}</strong></td>
                <td>${item.quantity}</td>
                <td>${item. reorder_level}</td>
                <td>
                    <span class="badge badge-danger">
                        <i class="fas fa-exclamation-triangle"></i> Low Stock
                    </span>
                </td>
            </tr>
        `).join('');
    } else {
        tbody.innerHTML = '<tr><td colspan="4" class="text-center">All products are well stocked! </td></tr>';
    }
}
</script>