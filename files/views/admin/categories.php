<div class="page-header">
    <div class="page-header-actions">
        <div>
            <h1><i class="fas fa-tags"></i> Category Management</h1>
            <p>Manage product categories</p>
        </div>
        <button class="btn btn-primary" onclick="openModal('add-category-modal')">
            <i class="fas fa-plus"></i> Add Category
        </button>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h2><i class="fas fa-list"></i> All Categories</h2>
    </div>
    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Products</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="categories-list">
                <tr>
                    <td colspan="6" class="text-center">Loading... </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Add Category Modal -->
<div class="modal-overlay" id="add-category-modal">
    <div class="modal">
        <div class="modal-header">
            <h3><i class="fas fa-plus"></i> Add New Category</h3>
            <button class="modal-close" onclick="closeModal('add-category-modal')">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form id="add-category-form" onsubmit="handleAddCategory(event)">
            <div class="modal-body">
                <div class="form-group">
                    <label for="category_name">Category Name *</label>
                    <input type="text" id="category_name" class="form-control" required placeholder="Enter category name">
                </div>
                <div class="form-group">
                    <label for="category_description">Description</label>
                    <textarea id="category_description" class="form-control" placeholder="Enter category description"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('add-category-modal')">Cancel</button>
                <button type="submit" class="btn btn-primary">Add Category</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Category Modal -->
<div class="modal-overlay" id="edit-category-modal">
    <div class="modal">
        <div class="modal-header">
            <h3><i class="fas fa-edit"></i> Edit Category</h3>
            <button class="modal-close" onclick="closeModal('edit-category-modal')">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form id="edit-category-form" onsubmit="handleUpdateCategory(event)">
            <input type="hidden" id="edit_category_id">
            <div class="modal-body">
                <div class="form-group">
                    <label for="edit_category_name">Category Name *</label>
                    <input type="text" id="edit_category_name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="edit_category_description">Description</label>
                    <textarea id="edit_category_description" class="form-control"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('edit-category-modal')">Cancel</button>
                <button type="submit" class="btn btn-primary">Update Category</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    loadCategories();
});

async function loadCategories() {
    const result = await apiRequest('get_categories');
    const tbody = document.getElementById('categories-list');
    
    if (result.success && result.data.length > 0) {
        tbody.innerHTML = result.data.map(cat => `
            <tr>
                <td>#${cat.id}</td>
                <td><strong>${cat.name}</strong></td>
                <td>${cat.description || 'No description'}</td>
                <td><span class="badge badge-primary">${cat.product_count || 0} products</span></td>
                <td>${formatDate(cat.created_at)}</td>
                <td>
                    <div class="action-buttons">
                        <button class="btn btn-sm btn-secondary" onclick="editCategory(${cat.id})" title="Edit">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="deleteCategory(${cat.id})" title="Delete">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `).join('');
    } else {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center">No categories found</td></tr>';
    }
}

async function editCategory(id) {
    const result = await apiRequest('get_category', { id });
    if (result.success) {
        document.getElementById('edit_category_id').value = result.data. id;
        document.getElementById('edit_category_name').value = result.data. name;
        document.getElementById('edit_category_description').value = result.data. description || '';
        openModal('edit-category-modal');
    }
}
</script>