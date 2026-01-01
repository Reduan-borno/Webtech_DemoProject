<div class="page-header">
    <div class="page-header-actions">
        <div>
            <h1><i class="fas fa-users"></i> Customer Management</h1>
            <p>Add and manage customers</p>
        </div>
        <button class="btn btn-primary" onclick="openModal('add-customer-modal')">
            <i class="fas fa-user-plus"></i> Add Customer
        </button>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h2><i class="fas fa-list"></i> All Customers</h2>
        <div class="search-input" style="width: 300px; margin: 0;">
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
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="customers-list">
                <tr>
                    <td colspan="7" class="text-center">Loading...</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Add Customer Modal -->
<div class="modal-overlay" id="add-customer-modal">
    <div class="modal">
        <div class="modal-header">
            <h3><i class="fas fa-user-plus"></i> Add New Customer</h3>
            <button class="modal-close" onclick="closeModal('add-customer-modal')">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form id="add-customer-form" onsubmit="handleAddCustomer(event)">
            <div class="modal-body">
                <div class="form-group">
                    <label for="cust_full_name">Full Name *</label>
                    <input type="text" id="cust_full_name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="cust_username">Username *</label>
                    <input type="text" id="cust_username" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="cust_email">Email *</label>
                    <input type="email" id="cust_email" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="cust_password">Password *</label>
                    <input type="password" id="cust_password" class="form-control" required minlength="6">
                </div>
                <div class="form-group">
                    <label for="cust_phone">Phone</label>
                    <input type="tel" id="cust_phone" class="form-control">
                </div>
                <div class="form-group">
                    <label for="cust_address">Address</label>
                    <textarea id="cust_address" class="form-control"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('add-customer-modal')">Cancel</button>
                <button type="submit" class="btn btn-primary">Add Customer</button>
            </div>
        </form>
    </div>
</div>

<script>
let allCustomers = [];

document.addEventListener('DOMContentLoaded', function() {
    loadCustomers();
});

async function loadCustomers() {
    const result = await apiRequest('get_customers');
    const tbody = document. getElementById('customers-list');
    
    if (result.success && result.data. length > 0) {
        allCustomers = result.data;
        displayCustomers(result.data);
    } else {
        tbody. innerHTML = '<tr><td colspan="7" class="text-center">No customers found</td></tr>';
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
                <td>${cust.email}</td>
                <td>${cust.phone || 'N/A'}</td>
                <td>${cust. address || 'N/A'}</td>
                <td>
                    <div class="action-buttons">
                        <button class="btn btn-sm btn-danger" onclick="removeCustomer(${cust.id})" title="Remove">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `).join('');
    } else {
        tbody.innerHTML = '<tr><td colspan="7" class="text-center">No customers found</td></tr>';
    }
}

function filterCustomers() {
    const searchTerm = document. getElementById('customer-search').value.toLowerCase();
    const filtered = allCustomers.filter(cust => 
        cust. full_name.toLowerCase().includes(searchTerm) ||
        cust.email.toLowerCase().includes(searchTerm)
    );
    displayCustomers(filtered);
}

async function handleAddCustomer(event) {
    event.preventDefault();
    
    const data = {
        full_name: document. getElementById('cust_full_name').value,
        username:  document.getElementById('cust_username').value,
        email:  document.getElementById('cust_email').value,
        password: document.getElementById('cust_password').value,
        phone: document.getElementById('cust_phone').value,
        address: document.getElementById('cust_address').value
    };
    
    showLoading();
    const result = await apiRequest('add_customer', data);
    hideLoading();
    
    if (result.success) {
        showToast(result.message);
        closeModal('add-customer-modal');
        document.getElementById('add-customer-form').reset();
        loadCustomers();
    } else {
        showToast(result. message, 'error');
    }
}

async function removeCustomer(id) {
    if (! confirm('Are you sure you want to remove this customer?')) return;
    
    showLoading();
    const result = await apiRequest('remove_customer', { id });
    hideLoading();
    
    if (result. success) {
        showToast(result.message);
        loadCustomers();
    } else {
        showToast(result.message, 'error');
    }
}
</script>