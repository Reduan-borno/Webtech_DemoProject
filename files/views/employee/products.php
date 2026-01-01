<div class="page-header">
    <div class="page-header-actions">
        <div>
            <h1><i class="fas fa-box"></i> Product Management</h1>
            <p>Add, edit, and manage products</p>
        </div>
        <button class="btn btn-primary" onclick="openModal('add-product-modal')">
            <i class="fas fa-plus"></i> Add Product
        </button>
    </div>
</div>

<div class="search-box">
    <div class="search-input">
        <i class="fas fa-search"></i>
        <input type="text" id="product-search" placeholder="Search products..." onkeyup="filterProducts()">
    </div>
    <select id="category-filter" class="filter-select" onchange="filterProducts()">
        <option value="">All Categories</option>
    </select>
</div>

<div class="card">
    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Product</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="products-list">
                <tr>
                    <td colspan="6" class="text-center">Loading...</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Add Product Modal -->
<div class="modal-overlay" id="add-product-modal">
    <div class="modal">
        <div class="modal-header">
            <h3><i class="fas fa-plus"></i> Add New Product</h3>
            <button class="modal-close" onclick="closeModal('add-product-modal')">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form id="add-product-form" onsubmit="handleAddProduct(event)">
            <div class="modal-body">
                <div class="form-group">
                    <label for="product_name">Product Name *</label>
                    <input type="text" id="product_name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="product_category">Category *</label>
                    <select id="product_category" class="form-control" required>
                        <option value="">Select Category</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="product_price">Price ($) *</label>
                    <input type="number" id="product_price" class="form-control" step="0.01" min="0" required>
                </div>
                <div class="form-group">
                    <label for="product_description">Description</label>
                    <textarea id="product_description" class="form-control"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('add-product-modal')">Cancel</button>
                <button type="submit" class="btn btn-primary">Add Product</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Product Modal -->
<div class="modal-overlay" id="edit-product-modal">
    <div class="modal">
        <div class="modal-header">
            <h3><i class="fas fa-edit"></i> Edit Product</h3>
            <button class="modal-close" onclick="closeModal('edit-product-modal')">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form id="edit-product-form" onsubmit="handleUpdateProduct(event)">
            <input type="hidden" id="edit_product_id">
            <div class="modal-body">
                <div class="form-group">
                    <label for="edit_product_name">Product Name *</label>
                    <input type="text" id="edit_product_name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="edit_product_category">Category *</label>
                    <select id="edit_product_category" class="form-control" required>
                        <option value="">Select Category</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="edit_product_price">Price ($) *</label>
                    <input type="number" id="edit_product_price" class="form-control" step="0.01" min="0" required>
                </div>
                <div class="form-group">
                    <label for="edit_product_description">Description</label>
                    <textarea id="edit_product_description" class="form-control"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('edit-product-modal')">Cancel</button>
                <button type="submit" class="btn btn-primary">Update Product</button>
            </div>
        </form>
    </div>
</div>

<!-- Update Price Modal -->
<div class="modal-overlay" id="update-price-modal">
    <div class="modal">
        <div class="modal-header">
            <h3><i class="fas fa-dollar-sign"></i> Update Price</h3>
            <button class="modal-close" onclick="closeModal('update-price-modal')">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form id="update-price-form" onsubmit="handleUpdatePrice(event)">
            <input type="hidden" id="price_product_id">
            <div class="modal-body">
                <div class="form-group">
                    <label>Product:  <strong id="price_product_name"></strong></label>
                </div>
                <div class="form-group">
                    <label for="new_price">New Price ($) *</label>
                    <input type="number" id="new_price" class="form-control" step="0.01" min="0" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('update-price-modal')">Cancel</button>
                <button type="submit" class="btn btn-primary">Update Price</button>
            </div>
        </form>
    </div>
</div>

<script>
let allProducts = [];
let categories = [];

document.addEventListener('DOMContentLoaded', function() {
    loadProducts();
    loadCategoriesForSelect();
});

async function loadProducts() {
    const result = await apiRequest('get_products');
    const tbody = document.getElementById('products-list');
    
    if (result. success && result.data.length > 0) {
        allProducts = result.data;
        displayProducts(result.data);
    } else {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center">No products found</td></tr>';
    }
}

async function loadCategoriesForSelect() {
    const result = await apiRequest('get_categories');
    if (result.success) {
        categories = result.data;
        const options = categories.map(cat => `<option value="${cat.id}">${cat.name}</option>`).join('');
        document.getElementById('product_category').innerHTML = '<option value="">Select Category</option>' + options;
        document.getElementById('edit_product_category').innerHTML = '<option value="">Select Category</option>' + options;
        document.getElementById('category-filter').innerHTML = '<option value="">All Categories</option>' + options;
    }
}

function displayProducts(products) {
    const tbody = document.getElementById('products-list');
    
    if (products.length > 0) {
        tbody.innerHTML = products.map(prod => `
            <tr>
                <td>#${prod.id}</td>
                <td>
                    <strong>${prod.name}</strong>
                    <br><small class="text-muted">${prod.description ?  prod.description. substring(0, 50) + '...' : 'No description'}</small>
                </td>
                <td><span class="badge badge-primary">${prod.category_name || 'Uncategorized'}</span></td>
                <td><strong>$${parseFloat(prod.price).toFixed(2)}</strong></td>
                <td>
                    <span class="badge ${prod.stock > 10 ? 'badge-success' : prod.stock > 0 ? 'badge-warning' : 'badge-danger'}">
                        ${prod.stock || 0} units
                    </span>
                </td>
                <td>
                    <div class="action-buttons">
                        <button class="btn btn-sm btn-secondary" onclick="openPriceModal(${prod.id}, '${prod.name}', ${prod.price})" title="Update Price">
                            <i class="fas fa-dollar-sign"></i>
                        </button>
                        <button class="btn btn-sm btn-primary" onclick="editProduct(${prod. id})" title="Edit">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="deleteProduct(${prod. id})" title="Delete">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `).join('');
    } else {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center">No products found</td></tr>';
    }
}

function filterProducts() {
    const searchTerm = document.getElementById('product-search').value.toLowerCase();
    const categoryId = document.getElementById('category-filter').value;
    
    let filtered = allProducts;
    
    if (searchTerm) {
        filtered = filtered.filter(prod => 
            prod.name.toLowerCase().includes(searchTerm) ||
            (prod.description && prod.description.toLowerCase().includes(searchTerm))
        );
    }
    
    if (categoryId) {
        filtered = filtered.filter(prod => prod.category_id == categoryId);
    }
    
    displayProducts(filtered);
}

async function editProduct(id) {
    const result = await apiRequest('get_product', { id });
    if (result.success) {
        const product = result.data;
        document.getElementById('edit_product_id').value = product.id;
        document.getElementById('edit_product_name').value = product.name;
        document. getElementById('edit_product_category').value = product.category_id || '';
        document.getElementById('edit_product_price').value = product.price;
        document.getElementById('edit_product_description').value = product.description || '';
        openModal('edit-product-modal');
    }
}

function openPriceModal(id, name, currentPrice) {
    document.getElementById('price_product_id').value = id;
    document.getElementById('price_product_name').textContent = name;
    document.getElementById('new_price').value = currentPrice;
    openModal('update-price-modal');
}

async function handleUpdatePrice(event) {
    event.preventDefault();
    
    const productId = document.getElementById('price_product_id').value;
    const newPrice = document.getElementById('new_price').value;
    
    showLoading();
    const result = await apiRequest('update_price', { product_id: productId, price: newPrice });
    hideLoading();
    
    if (result.success) {
        showToast(result.message);
        closeModal('update-price-modal');
        loadProducts();
    } else {
        showToast(result.message, 'error');
    }
}

async function deleteProduct(id) {
    if (! confirm('Are you sure you want to delete this product?')) return;
    
    showLoading();
    const result = await apiRequest('remove_product', { id });
    hideLoading();
    
    if (result. success) {
        showToast(result.message);
        loadProducts();
    } else {
        showToast(result.message, 'error');
    }
}
</script>