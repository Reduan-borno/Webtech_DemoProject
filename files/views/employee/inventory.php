<div class="page-header">
    <div class="page-header-actions">
        <div>
            <h1><i class="fas fa-warehouse"></i> Inventory Management</h1>
            <p>Manage stock levels for all products</p>
        </div>
        <div class="action-buttons">
            <button class="btn btn-success" onclick="openModal('stock-in-modal')">
                <i class="fas fa-arrow-down"></i> Stock In
            </button>
            <button class="btn btn-warning" onclick="openModal('stock-out-modal')">
                <i class="fas fa-arrow-up"></i> Stock Out
            </button>
        </div>
    </div>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon primary">
            <i class="fas fa-boxes"></i>
        </div>
        <div class="stat-details">
            <h3 id="total-products">0</h3>
            <p>Total Products</p>
        </div>
    </div>
    <div class="stat-card danger">
        <div class="stat-icon danger">
            <i class="fas fa-exclamation-triangle"></i>
        </div>
        <div class="stat-details">
            <h3 id="low-stock-count">0</h3>
            <p>Low Stock Items</p>
        </div>
    </div>
    <div class="stat-card success">
        <div class="stat-icon success">
            <i class="fas fa-check-circle"></i>
        </div>
        <div class="stat-details">
            <h3 id="well-stocked">0</h3>
            <p>Well Stocked</p>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h2><i class="fas fa-list"></i> Inventory Status</h2>
        <select id="stock-filter" class="filter-select" onchange="filterInventory()">
            <option value="">All Items</option>
            <option value="low">Low Stock</option>
            <option value="out">Out of Stock</option>
            <option value="ok">Well Stocked</option>
        </select>
    </div>
    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Category</th>
                    <th>Current Stock</th>
                    <th>Reorder Level</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="inventory-list">
                <tr>
                    <td colspan="6" class="text-center">Loading...</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Stock In Modal -->
<div class="modal-overlay" id="stock-in-modal">
    <div class="modal">
        <div class="modal-header">
            <h3><i class="fas fa-arrow-down"></i> Stock In</h3>
            <button class="modal-close" onclick="closeModal('stock-in-modal')">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form id="stock-in-form" onsubmit="handleStockIn(event)">
            <div class="modal-body">
                <div class="form-group">
                    <label for="stock_in_product">Select Product *</label>
                    <select id="stock_in_product" class="form-control" required>
                        <option value="">Select Product</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="stock_in_quantity">Quantity *</label>
                    <input type="number" id="stock_in_quantity" class="form-control" min="1" required>
                </div>
                <div class="form-group">
                    <label for="stock_in_notes">Notes</label>
                    <textarea id="stock_in_notes" class="form-control" placeholder="Optional notes... "></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('stock-in-modal')">Cancel</button>
                <button type="submit" class="btn btn-success">Add Stock</button>
            </div>
        </form>
    </div>
</div>

<!-- Stock Out Modal -->
<div class="modal-overlay" id="stock-out-modal">
    <div class="modal">
        <div class="modal-header">
            <h3><i class="fas fa-arrow-up"></i> Stock Out</h3>
            <button class="modal-close" onclick="closeModal('stock-out-modal')">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form id="stock-out-form" onsubmit="handleStockOut(event)">
            <div class="modal-body">
                <div class="form-group">
                    <label for="stock_out_product">Select Product *</label>
                    <select id="stock_out_product" class="form-control" required>
                        <option value="">Select Product</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="stock_out_quantity">Quantity *</label>
                    <input type="number" id="stock_out_quantity" class="form-control" min="1" required>
                </div>
                <div class="form-group">
                    <label for="stock_out_notes">Notes</label>
                    <textarea id="stock_out_notes" class="form-control" placeholder="Reason for stock out..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('stock-out-modal')">Cancel</button>
                <button type="submit" class="btn btn-warning">Remove Stock</button>
            </div>
        </form>
    </div>
</div>

<script>
let inventoryData = [];

document.addEventListener('DOMContentLoaded', function() {
    loadInventory();
});

async function loadInventory() {
    const result = await apiRequest('get_products');
    
    if (result. success) {
        inventoryData = result.data;
        displayInventory(result.data);
        updateStats(result.data);
        populateProductSelects(result.data);
    }
}

function displayInventory(products) {
    const tbody = document.getElementById('inventory-list');
    
    if (products.length > 0) {
        tbody.innerHTML = products.map(prod => {
            const stock = prod.stock || 0;
            const reorderLevel = 10;
            let status, statusClass;
            
            if (stock === 0) {
                status = 'Out of Stock';
                statusClass = 'badge-danger';
            } else if (stock <= reorderLevel) {
                status = 'Low Stock';
                statusClass = 'badge-warning';
            } else {
                status = 'In Stock';
                statusClass = 'badge-success';
            }
            
            return `
                <tr>
                    <td><strong>${prod.name}</strong></td>
                    <td>${prod.category_name || 'Uncategorized'}</td>
                    <td><strong>${stock}</strong> units</td>
                    <td>${reorderLevel} units</td>
                    <td><span class="badge ${statusClass}">${status}</span></td>
                    <td>
                        <div class="action-buttons">
                            <button class="btn btn-sm btn-success" onclick="quickStockIn(${prod.id}, '${prod.name}')" title="Stock In">
                                <i class="fas fa-plus"></i>
                            </button>
                            <button class="btn btn-sm btn-warning" onclick="quickStockOut(${prod.id}, '${prod.name}')" title="Stock Out">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        }).join('');
    } else {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center">No products found</td></tr>';
    }
}

function updateStats(products) {
    const total = products.length;
    const lowStock = products.filter(p => (p.stock || 0) <= 10 && (p.stock || 0) > 0).length;
    const outOfStock = products. filter(p => (p.stock || 0) === 0).length;
    const wellStocked = products.filter(p => (p.stock || 0) > 10).length;
    
    document.getElementById('total-products').textContent = total;
    document.getElementById('low-stock-count').textContent = lowStock + outOfStock;
    document.getElementById('well-stocked').textContent = wellStocked;
}

function populateProductSelects(products) {
    const options = products.map(p => `<option value="${p. id}">${p. name} (Stock: ${p.stock || 0})</option>`).join('');
    document.getElementById('stock_in_product').innerHTML = '<option value="">Select Product</option>' + options;
    document. getElementById('stock_out_product').innerHTML = '<option value="">Select Product</option>' + options;
}

function filterInventory() {
    const filter = document.getElementById('stock-filter').value;
    let filtered = inventoryData;
    
    switch (filter) {
        case 'low': 
            filtered = inventoryData.filter(p => (p.stock || 0) <= 10 && (p.stock || 0) > 0);
            break;
        case 'out': 
            filtered = inventoryData.filter(p => (p.stock || 0) === 0);
            break;
        case 'ok': 
            filtered = inventoryData.filter(p => (p.stock || 0) > 10);
            break;
    }
    
    displayInventory(filtered);
}

function quickStockIn(productId, productName) {
    document.getElementById('stock_in_product').value = productId;
    openModal('stock-in-modal');
}

function quickStockOut(productId, productName) {
    document.getElementById('stock_out_product').value = productId;
    openModal('stock-out-modal');
}

async function handleStockIn(event) {
    event.preventDefault();
    
    const data = {
        product_id: document. getElementById('stock_in_product').value,
        quantity: document.getElementById('stock_in_quantity').value,
        notes: document.getElementById('stock_in_notes').value
    };
    
    showLoading();
    const result = await apiRequest('stock_in', data);
    hideLoading();
    
    if (result.success) {
        showToast(result. message);
        closeModal('stock-in-modal');
        document.getElementById('stock-in-form').reset();
        loadInventory();
    } else {
        showToast(result. message, 'error');
    }
}

async function handleStockOut(event) {
    event.preventDefault();
    
    const data = {
        product_id: document.getElementById('stock_out_product').value,
        quantity: document. getElementById('stock_out_quantity').value,
        notes: document.getElementById('stock_out_notes').value
    };
    
    showLoading();
    const result = await apiRequest('stock_out', data);
    hideLoading();
    
    if (result. success) {
        showToast(result.message);
        closeModal('stock-out-modal');
        document.getElementById('stock-out-form').reset();
        loadInventory();
    } else {
        showToast(result.message, 'error');
    }
}
</script>