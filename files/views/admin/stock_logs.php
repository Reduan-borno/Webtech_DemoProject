<div class="page-header">
    <h1><i class="fas fa-clipboard-list"></i> Stock Activity Logs</h1>
    <p>View all stock in and stock out activities</p>
</div>

<div class="card">
    <div class="card-header">
        <h2><i class="fas fa-history"></i> Activity History</h2>
        <div class="filter-group">
            <select id="filter-action" class="filter-select" onchange="filterLogs()">
                <option value="">All Actions</option>
                <option value="stock_in">Stock In</option>
                <option value="stock_out">Stock Out</option>
            </select>
        </div>
    </div>
    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Product</th>
                    <th>Action</th>
                    <th>Quantity</th>
                    <th>Employee</th>
                    <th>Notes</th>
                    <th>Date & Time</th>
                </tr>
            </thead>
            <tbody id="stock-logs-list">
                <tr>
                    <td colspan="7" class="text-center">Loading...</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<script>
let allLogs = [];

document.addEventListener('DOMContentLoaded', function() {
    loadStockLogs();
});

async function loadStockLogs() {
    const result = await apiRequest('get_stock_logs');
    const tbody = document.getElementById('stock-logs-list');
    
    if (result.success && result.data. length > 0) {
        allLogs = result. data;
        displayLogs(result. data);
    } else {
        tbody.innerHTML = '<tr><td colspan="7" class="text-center">No stock logs found</td></tr>';
    }
}

function displayLogs(logs) {
    const tbody = document.getElementById('stock-logs-list');
    
    if (logs.length > 0) {
        tbody.innerHTML = logs.map(log => `
            <tr>
                <td>#${log. id}</td>
                <td><strong>${log.product_name}</strong></td>
                <td>
                    <span class="badge ${log.action === 'stock_in' ? 'badge-success' :  'badge-danger'}">
                        <i class="fas ${log.action === 'stock_in' ? 'fa-arrow-down' : 'fa-arrow-up'}"></i>
                        ${log.action === 'stock_in' ? 'Stock In' : 'Stock Out'}
                    </span>
                </td>
                <td><strong>${log.quantity}</strong></td>
                <td>${log.employee_name}</td>
                <td>${log.notes || 'N/A'}</td>
                <td>${formatDate(log.created_at)}</td>
            </tr>
        `).join('');
    } else {
        tbody.innerHTML = '<tr><td colspan="7" class="text-center">No logs found</td></tr>';
    }
}

function filterLogs() {
    const action = document.getElementById('filter-action').value;
    
    if (action) {
        const filtered = allLogs. filter(log => log.action === action);
        displayLogs(filtered);
    } else {
        displayLogs(allLogs);
    }
}
</script>