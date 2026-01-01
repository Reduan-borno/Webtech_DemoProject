/**
 * GadgetGrid - Main JavaScript File
 * Handles AJAX requests, UI interactions, and dynamic functionality
 */

// API Base URL
const API_URL = 'api/handler. php';

// ============================================
// Utility Functions
// ============================================

/**
 * Make AJAX request
 */
async function apiRequest(action, data = {}) {
    try {
        const formData = new FormData();
        formData.append('action', action);
        
        for (const key in data) {
            if (typeof data[key] === 'object') {
                formData. append(key, JSON.stringify(data[key]));
            } else {
                formData.append(key, data[key]);
            }
        }

        const response = await fetch(API_URL, {
            method: 'POST',
            body: formData
        });

        const result = await response. json();
        return result;
    } catch (error) {
        console.error('API Error:', error);
        showToast('An error occurred.  Please try again. ', 'error');
        return { success: false, message: error.message };
    }
}

/**
 * Show toast notification
 */
function showToast(message, type = 'success') {
    const container = document.getElementById('toast-container') || createToastContainer();
    
    const toast = document.createElement('div');
    toast.className = `toast ${type}`;
    toast.innerHTML = `
        <i class="fas ${type === 'success' ? 'fa-check-circle' : type === 'error' ? 'fa-times-circle' : 'fa-exclamation-circle'}"></i>
        <span>${message}</span>
    `;
    
    container. appendChild(toast);
    
    setTimeout(() => {
        toast.style.animation = 'slideIn 0.3s ease reverse';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

function createToastContainer() {
    const container = document.createElement('div');
    container.id = 'toast-container';
    container.className = 'toast-container';
    document.body.appendChild(container);
    return container;
}

/**
 * Show loading overlay
 */
function showLoading() {
    let overlay = document.getElementById('loading-overlay');
    if (!overlay) {
        overlay = document.createElement('div');
        overlay.id = 'loading-overlay';
        overlay.className = 'loading-overlay';
        overlay.innerHTML = '<div class="spinner"></div>';
        document. body.appendChild(overlay);
    }
    overlay.style. display = 'flex';
}

function hideLoading() {
    const overlay = document.getElementById('loading-overlay');
    if (overlay) {
        overlay.style.display = 'none';
    }
}

/**
 * Open modal
 */
function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }
}

/**
 * Close modal
 */
function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove('active');
        document.body.style.overflow = '';
    }
}

/**
 * Format currency
 */
function formatCurrency(amount) {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD'
    }).format(amount);
}

/**
 * Format date
 */
function formatDate(dateString) {
    const options = { year: 'numeric', month: 'short', day: 'numeric' };
    return new Date(dateString).toLocaleDateString('en-US', options);
}

// ============================================
// Authentication Functions
// ============================================

/**
 * Handle login form submission
 */
async function handleLogin(event) {
    event.preventDefault();
    
    const username = document.getElementById('username').value;
    const password = document.getElementById('password').value;
    
    if (!username || !password) {
        showToast('Please fill in all fields', 'error');
        return;
    }
    
    showLoading();
    
    const result = await apiRequest('login', { username, password });
    
    hideLoading();
    
    if (result.success) {
        showToast('Login successful!  Redirecting...');
        setTimeout(() => {
            switch (result.role) {
                case 'admin':
                    window.location. href = 'index.php? page=admin_dashboard';
                    break;
                case 'employee':
                    window.location.href = 'index.php?page=employee_dashboard';
                    break;
                case 'customer':
                    window.location.href = 'index.php?page=customer_dashboard';
                    break;
            }
        }, 1000);
    } else {
        showToast(result.message, 'error');
    }
}

/**
 * Handle registration form submission
 */
async function handleRegister(event) {
    event.preventDefault();
    
    const formData = {
        username: document.getElementById('username').value,
        email: document.getElementById('email').value,
        password: document. getElementById('password').value,
        confirm_password: document.getElementById('confirm_password').value,
        full_name: document. getElementById('full_name').value,
        phone: document.getElementById('phone')?.value || '',
        role: document.querySelector('input[name="role"]:checked')?.value || 'customer'
    };
    
    // Validation
    if (!formData.username || ! formData.email || !formData.password || !formData.full_name) {
        showToast('Please fill in all required fields', 'error');
        return;
    }
    
    if (formData.password !== formData.confirm_password) {
        showToast('Passwords do not match', 'error');
        return;
    }
    
    if (formData.password.length < 6) {
        showToast('Password must be at least 6 characters', 'error');
        return;
    }
    
    showLoading();
    
    const result = await apiRequest('register', formData);
    
    hideLoading();
    
    if (result.success) {
        showToast(result.message);
        setTimeout(() => {
            window.location. href = 'index.php?page=login';
        }, 2000);
    } else {
        showToast(result. message, 'error');
    }
}

/**
 * Handle logout
 */
async function handleLogout() {
    showLoading();
    await apiRequest('logout');
    hideLoading();
    window.location.href = 'index.php? page=login';
}

/**
 * Handle password change
 */
async function handleChangePassword(event) {
    event.preventDefault();
    
    const currentPassword = document.getElementById('current_password').value;
    const newPassword = document. getElementById('new_password').value;
    const confirmPassword = document.getElementById('confirm_new_password').value;
    
    if (!currentPassword || ! newPassword || !confirmPassword) {
        showToast('Please fill in all fields', 'error');
        return;
    }
    
    if (newPassword !== confirmPassword) {
        showToast('New passwords do not match', 'error');
        return;
    }
    
    if (newPassword.length < 6) {
        showToast('Password must be at least 6 characters', 'error');
        return;
    }
    
    showLoading();
    
    const result = await apiRequest('change_password', {
        current_password:  currentPassword,
        new_password: newPassword
    });
    
    hideLoading();
    
    if (result.success) {
        showToast(result.message);
        closeModal('change-password-modal');
        document.getElementById('change-password-form').reset();
    } else {
        showToast(result.message, 'error');
    }
}

// ============================================
// Admin Functions
// ============================================

/**
 * Load admin dashboard stats
 */
async function loadAdminDashboard() {
    const result = await apiRequest('get_admin_stats');
    if (result.success) {
        updateDashboardStats(result.data);
    }
}

/**
 * Load pending employees
 */
async function loadPendingEmployees() {
    const result = await apiRequest('get_pending_employees');
    if (result. success) {
        displayPendingEmployees(result.data);
    }
}

/**
 * Approve employee
 */
async function approveEmployee(employeeId) {
    if (!confirm('Are you sure you want to approve this employee?')) return;
    
    showLoading();
    const result = await apiRequest('approve_employee', { employee_id:  employeeId });
    hideLoading();
    
    if (result.success) {
        showToast(result.message);
        loadPendingEmployees();
        loadAllEmployees();
    } else {
        showToast(result.message, 'error');
    }
}

/**
 * Reject employee
 */
async function rejectEmployee(employeeId) {
    if (! confirm('Are you sure you want to reject this employee?')) return;
    
    showLoading();
    const result = await apiRequest('reject_employee', { employee_id: employeeId });
    hideLoading();
    
    if (result.success) {
        showToast(result. message);
        loadPendingEmployees();
    } else {
        showToast(result.message, 'error');
    }
}

/**
 * Load all employees
 */
async function loadAllEmployees() {
    showLoading();
    const result = await apiRequest('get_all_employees');
    hideLoading();
    
    if (result.success) {
        displayEmployees(result.data);
    }
}

/**
 * Load all customers (admin view)
 */
async function loadAdminCustomers() {
    showLoading();
    const result = await apiRequest('get_admin_customers');
    hideLoading();
    
    if (result.success) {
        displayAdminCustomers(result.data);
    }
}

/**
 * Load categories
 */
async function loadCategories() {
    showLoading();
    const result = await apiRequest('get_categories');
    hideLoading();
    
    if (result.success) {
        displayCategories(result. data);
    }
}

/**
 * Add category
 */
async function handleAddCategory(event) {
    event. preventDefault();
    
    const name = document.getElementById('category_name').value;
    const description = document.getElementById('category_description').value;
    
    if (!name) {
        showToast('Category name is required', 'error');
        return;
    }
    
    showLoading();
    const result = await apiRequest('add_category', { name, description });
    hideLoading();
    
    if (result. success) {
        showToast(result.message);
        closeModal('add-category-modal');
        document.getElementById('add-category-form').reset();
        loadCategories();
    } else {
        showToast(result.message, 'error');
    }
}

/**
 * Edit category
 */
async function editCategory(id) {
    const result = await apiRequest('get_category', { id });
    if (result.success) {
        document.getElementById('edit_category_id').value = result.data.id;
        document.getElementById('edit_category_name').value = result.data.name;
        document.getElementById('edit_category_description').value = result.data.description || '';
        openModal('edit-category-modal');
    }
}

/**
 * Update category
 */
async function handleUpdateCategory(event) {
    event. preventDefault();
    
    const id = document.getElementById('edit_category_id').value;
    const name = document.getElementById('edit_category_name').value;
    const description = document. getElementById('edit_category_description').value;
    
    showLoading();
    const result = await apiRequest('update_category', { id, name, description });
    hideLoading();
    
    if (result.success) {
        showToast(result.message);
        closeModal('edit-category-modal');
        loadCategories();
    } else {
        showToast(result. message, 'error');
    }
}

/**
 * Delete category
 */
async function deleteCategory(id) {
    if (!confirm('Are you sure you want to delete this category?')) return;
    
    showLoading();
    const result = await apiRequest('delete_category', { id });
    hideLoading();
    
    if (result.success) {
        showToast(result. message);
        loadCategories();
    } else {
        showToast(result.message, 'error');
    }
}

/**
 * Load stock logs
 */
async function loadStockLogs() {
    showLoading();
    const result = await apiRequest('get_stock_logs');
    hideLoading();
    
    if (result.success) {
        displayStockLogs(result.data);
    }
}

// ============================================
// Employee Functions
// ============================================

/**
 * Load employee dashboard
 */
async function loadEmployeeDashboard() {
    const result = await apiRequest('get_employee_stats');
    if (result.success) {
        updateDashboardStats(result.data);
    }
}

/**
 * Load products (employee)
 */
async function loadProducts() {
    showLoading();
    const result = await apiRequest('get_products');
    hideLoading();
    
    if (result. success) {
        displayProducts(result. data);
    }
}

/**
 * Add product
 */
async function handleAddProduct(event) {
    event.preventDefault();
    
    const formData = {
        name: document.getElementById('product_name').value,
        description: document.getElementById('product_description').value,
        price: document.getElementById('product_price').value,
        category_id: document. getElementById('product_category').value,
        specifications: {}
    };
    
    if (! formData.name || !formData.price || !formData.category_id) {
        showToast('Please fill in all required fields', 'error');
        return;
    }
    
    showLoading();
    const result = await apiRequest('add_product', formData);
    hideLoading();
    
    if (result.success) {
        showToast(result.message);
        closeModal('add-product-modal');
        document.getElementById('add-product-form').reset();
        loadProducts();
    } else {
        showToast(result.message, 'error');
    }
}

/**
 * Edit product
 */
async function editProduct(id) {
    const result = await apiRequest('get_product', { id });
    if (result.success) {
        const product = result.data;
        document. getElementById('edit_product_id').value = product.id;
        document.getElementById('edit_product_name').value = product.name;
        document.getElementById('edit_product_description').value = product.description || '';
        document. getElementById('edit_product_price').value = product.price;
        document.getElementById('edit_product_category').value = product.category_id;
        openModal('edit-product-modal');
    }
}

/**
 * Update product
 */
async function handleUpdateProduct(event) {
    event.preventDefault();
    
    const formData = {
        id: document.getElementById('edit_product_id').value,
        name: document.getElementById('edit_product_name').value,
        description: document. getElementById('edit_product_description').value,
        price: document.getElementById('edit_product_price').value,
        category_id: document.getElementById('edit_product_category').value
    };
    
    showLoading();
    const result = await apiRequest('update_product', formData);
    hideLoading();
    
    if (result. success) {
        showToast(result.message);
        closeModal('edit-product-modal');
        loadProducts();
    } else {
        showToast(result.message, 'error');
    }
}

/**
 * Delete product
 */
async function deleteProduct(id) {
    if (!confirm('Are you sure you want to delete this product?')) return;
    
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

/**
 * Stock In
 */
async function handleStockIn(event) {
    event.preventDefault();
    
    const productId = document.getElementById('stock_product').value;
    const quantity = document.getElementById('stock_quantity').value;
    const notes = document.getElementById('stock_notes').value;
    
    if (!productId || !quantity) {
        showToast('Please select a product and enter quantity', 'error');
        return;
    }
    
    showLoading();
    const result = await apiRequest('stock_in', { 
        product_id: productId, 
        quantity: quantity,
        notes: notes 
    });
    hideLoading();
    
    if (result.success) {
        showToast(result.message);
        closeModal('stock-in-modal');
        document.getElementById('stock-in-form').reset();
        loadInventory();
    } else {
        showToast(result.message, 'error');
    }
}

/**
 * Stock Out
 */
async function handleStockOut(event) {
    event. preventDefault();
    
    const productId = document.getElementById('stockout_product').value;
    const quantity = document.getElementById('stockout_quantity').value;
    const notes = document.getElementById('stockout_notes').value;
    
    if (!productId || !quantity) {
        showToast('Please select a product and enter quantity', 'error');
        return;
    }