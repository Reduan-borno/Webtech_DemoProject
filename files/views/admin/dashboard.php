<div class="page-header">
    <h1><i class="fas fa-tachometer-alt"></i> Admin Dashboard</h1>
    <p>Welcome back!  Here's an overview of your system. </p>
</div>

<div class="stats-grid" id="admin-stats">
    <div class="stat-card">
        <div class="stat-icon primary">
            <i class="fas fa-user-tie"></i>
        </div>
        <div class="stat-details">
            <h3 id="stat-employees">0</h3>
            <p>Total Employees</p>
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
            <i class="fas fa-user-clock"></i>
        </div>
        <div class="stat-details">
            <h3 id="stat-pending">0</h3>
            <p>Pending Approvals</p>
        </div>
    </div>
    
    <div class="stat-card info">
        <div class="stat-icon info">
            <i class="fas fa-tags"></i>
        </div>
        <div class="stat-details">
            <h3 id="stat-categories">0</h3>
            <p>Categories</p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon primary">
            <i class="fas fa-box"></i>
        </div>
        <div class="stat-details">
            <h3 id="stat-products">0</h3>
            <p>Total Products</p>
        </div>
    </div>
    
    <div class="stat-card danger">
        <div class="stat-icon danger">
            <i class="fas fa-exclamation-triangle"></i>
        </div>
        <div class="stat-details">
            <h3 id="stat-lowstock">0</h3>
            <p>Low Stock Items</p>
        </div>
    </div>
</div>

<!-- Pending Approvals Section -->
<div class="card">
    <div class="card-header">
        <h2><i class="fas fa-user-clock"></i> Pending Employee Approvals</h2>
        <a href="index.php?page=employees" class="btn btn-sm btn-secondary">View All</a>
    </div>
    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Registered</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="pending-employees-table">
                <tr>
                    <td colspan="5" class="text-center">Loading...</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    loadAdminDashboard();
    loadPendingEmployeesPreview();
});

async function loadAdminDashboard() {
    const result = await apiRequest('get_admin_stats');
    if (result.success) {
        document. getElementById('stat-employees').textContent = result.data.total_employees || 0;
        document.getElementById('stat-customers').textContent = result.data. total_customers || 0;
        document.getElementById('stat-pending').textContent = result.data.pending_approvals || 0;
        document.getElementById('stat-categories').textContent = result.data.total_categories || 0;
        document. getElementById('stat-products').textContent = result.data.total_products || 0;
        document.getElementById('stat-lowstock').textContent = result.data.low_stock_count || 0;
    }
}

async function loadPendingEmployeesPreview() {
    const result = await apiRequest('get_pending_employees');
    const tbody = document.getElementById('pending-employees-table');
    
    if (result. success && result.data.length > 0) {
        tbody.innerHTML = result.data.slice(0, 5).map(emp => `
            <tr>
                <td>${emp.full_name}</td>
                <td>${emp.email}</td>
                <td>${emp.phone || 'N/A'}</td>
                <td>${formatDate(emp.created_at)}</td>
                <td>
                    <div class="action-buttons">
                        <button class="btn btn-sm btn-success" onclick="approveEmployee(${emp. id})">
                            <i class="fas fa-check"></i> Approve
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="rejectEmployee(${emp. id})">
                            <i class="fas fa-times"></i> Reject
                        </button>
                    </div>
                </td>
            </tr>
        `).join('');
    } else {
        tbody.innerHTML = '<tr><td colspan="5" class="text-center">No pending approvals</td></tr>';
    }
}
</script>