<div class="page-header">
    <h1><i class="fas fa-users"></i> Customer Management</h1>
    <p>View all registered customers</p>
</div>

<div class="card">
    <div class="card-header">
        <h2><i class="fas fa-list"></i> All Customers</h2>
        <div class="search-input" style="width: 300px; margin:  0;">
            <i class="fas fa-search"></i>
            <input type="text" id="customer-search" placeholder="Search customers..." onkeyup="filterCustomers()">
        </div>
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
                    <th>Address</th>
                    <th>Registered</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody id="customers-list">
                <tr>
                    <td colspan="8" class="text-center">Loading...</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<script>
let allCustomers = [];

document.addEventListener('DOMContentLoaded', function() {
    loadCustomers();
});

async function loadCustomers() {
    const result = await apiRequest('get_admin_customers');
    const tbody = document. getElementById('customers-list');
    
    if (result.success && result.data. length > 0) {
        allCustomers = result. data;
        displayCustomers(result.data);
    } else {
        tbody.innerHTML = '<tr><td colspan="8" class="text-center">No customers found</td></tr>';
    }
}

function displayCustomers(customers) {
    const tbody = document.getElementById('customers-list');
    
    if (customers.length > 0) {
        tbody.innerHTML = customers.map(cust => `
            <tr>
                <td>#${cust.id}</td>
                <td>${cust.full_name}</td>
                <td>${cust.username}</td>
                <td>${cust. email}</td>
                <td>${cust.phone || 'N/A'}</td>
                <td>${cust.address || 'N/A'}</td>
                <td>${formatDate(cust.created_at)}</td>
                <td>
                    <span class="badge badge-success">Active</span>
                </td>
            </tr>
        `).join('');
    } else {
        tbody.innerHTML = '<tr><td colspan="8" class="text-center">No customers found</td></tr>';
    }
}

function filterCustomers() {
    const searchTerm = document. getElementById('customer-search').value.toLowerCase();
    const filtered = allCustomers.filter(cust => 
        cust.full_name.toLowerCase().includes(searchTerm) ||
        cust. email.toLowerCase().includes(searchTerm) ||
        cust. username.toLowerCase().includes(searchTerm)
    );
    displayCustomers(filtered);
}
</script>