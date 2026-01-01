<div class="page-header">
    <div class="page-header-actions">
        <div>
            <h1><i class="fas fa-user-tie"></i> Employee Management</h1>
            <p>Manage and approve employee accounts</p>
        </div>
    </div>
</div>

<!-- Pending Approvals -->
<div class="card mb-20">
    <div class="card-header">
        <h2><i class="fas fa-user-clock"></i> Pending Approvals</h2>
    </div>
    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Registered</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="pending-employees-list">
                <tr>
                    <td colspan="7" class="text-center">Loading...</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- All Employees -->
<div class="card">
    <div class="card-header">
        <h2><i class="fas fa-users"></i> All Employees</h2>
    </div>
    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Status</th>
                    <th>Joined</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="all-employees-list">
                <tr>
                    <td colspan="8" class="text-center">Loading...</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    loadPendingEmployees();
    loadAllEmployees();
});

async function loadPendingEmployees() {
    const result = await apiRequest('get_pending_employees');
    const tbody = document. getElementById('pending-employees-list');
    
    if (result.success && result. data.length > 0) {
        tbody.innerHTML = result.data.map(emp => `
            <tr>
                <td>#${emp.id}</td>
                <td>${emp.full_name}</td>
                <td>${emp.username}</td>
                <td>${emp.email}</td>
                <td>${emp.phone || 'N/A'}</td>
                <td>${formatDate(emp. created_at)}</td>
                <td>
                    <div class="action-buttons">
                        <button class="btn btn-sm btn-success" onclick="approveEmployee(${emp.id})">
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
        tbody.innerHTML = '<tr><td colspan="7" class="text-center">No pending approvals</td></tr>';
    }
}

async function loadAllEmployees() {
    const result = await apiRequest('get_all_employees');
    const tbody = document.getElementById('all-employees-list');
    
    if (result.success && result.data.length > 0) {
        tbody.innerHTML = result.data. map(emp => `
            <tr>
                <td>#${emp.id}</td>
                <td>${emp.full_name}</td>
                <td>${emp.username}</td>
                <td>${emp.email}</td>
                <td>${emp.phone || 'N/A'}</td>
                <td>
                    <span class="badge ${emp.status === 'approved' ? 'badge-success' : emp.status === 'pending' ? 'badge-warning' : 'badge-danger'}">
                        ${emp.status}
                    </span>
                </td>
                <td>${formatDate(emp. created_at)}</td>
                <td>
                    <div class="action-buttons">
                        <button class="btn btn-sm btn-danger" onclick="deleteUser(${emp.id})" title="Delete">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `).join('');
    } else {
        tbody.innerHTML = '<tr><td colspan="8" class="text-center">No employees found</td></tr>';
    }
}

async function deleteUser(id) {
    if (! confirm('Are you sure you want to delete this user?')) return;
    
    showLoading();
    const result = await apiRequest('delete_user', { id });
    hideLoading();
    
    if (result. success) {
        showToast(result.message);
        loadAllEmployees();
    } else {
        showToast(result.message, 'error');
    }
}
</script>