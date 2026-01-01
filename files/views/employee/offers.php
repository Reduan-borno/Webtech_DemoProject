<div class="page-header">
    <div class="page-header-actions">
        <div>
            <h1><i class="fas fa-percent"></i> Offer Management</h1>
            <p>Create and manage product offers</p>
        </div>
        <button class="btn btn-primary" onclick="openModal('add-offer-modal')">
            <i class="fas fa-plus"></i> Create Offer
        </button>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h2><i class="fas fa-tags"></i> All Offers</h2>
        <select id="offer-filter" class="filter-select" onchange="filterOffers()">
            <option value="">All Offers</option>
            <option value="active">Active</option>
            <option value="expired">Expired</option>
        </select>
    </div>
    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Product</th>
                    <th>Discount</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="offers-list">
                <tr>
                    <td colspan="7" class="text-center">Loading...</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Add Offer Modal -->
<div class="modal-overlay" id="add-offer-modal">
    <div class="modal">
        <div class="modal-header">
            <h3><i class="fas fa-plus"></i> Create New Offer</h3>
            <button class="modal-close" onclick="closeModal('add-offer-modal')">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form id="add-offer-form" onsubmit="handleAddOffer(event)">
            <div class="modal-body">
                <div class="form-group">
                    <label for="offer_product">Select Product *</label>
                    <select id="offer_product" class="form-control" required>
                        <option value="">Select Product</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="offer_discount">Discount Percentage (%) *</label>
                    <input type="number" id="offer_discount" class="form-control" min="1" max="99" required placeholder="e.g., 20">
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="offer_start">Start Date *</label>
                        <input type="date" id="offer_start" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="offer_end">End Date *</label>
                        <input type="date" id="offer_end" class="form-control" required>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('add-offer-modal')">Cancel</button>
                <button type="submit" class="btn btn-primary">Create Offer</button>
            </div>
        </form>
    </div>
</div>

<script>
let allOffers = [];

document.addEventListener('DOMContentLoaded', function() {
    loadOffers();
    loadProductsForOffers();
    setDefaultDates();
});

function setDefaultDates() {
    const today = new Date().toISOString().split('T')[0];
    document. getElementById('offer_start').value = today;
    document.getElementById('offer_start').min = today;
    document.getElementById('offer_end').min = today;
}

async function loadOffers() {
    const result = await apiRequest('get_offers');
    const tbody = document.getElementById('offers-list');
    
    if (result.success && result.data.length > 0) {
        allOffers = result.data;
        displayOffers(result.data);
    } else {
        tbody.innerHTML = '<tr><td colspan="7" class="text-center">No offers found</td></tr>';
    }
}

async function loadProductsForOffers() {
    const result = await apiRequest('get_products');
    if (result.success) {
        const options = result.data. map(p => `<option value="${p.id}">${p.name} - $${parseFloat(p.price).toFixed(2)}</option>`).join('');
        document.getElementById('offer_product').innerHTML = '<option value="">Select Product</option>' + options;
    }
}

function displayOffers(offers) {
    const tbody = document.getElementById('offers-list');
    const today = new Date().toISOString().split('T')[0];
    
    if (offers.length > 0) {
        tbody.innerHTML = offers.map(offer => {
            const isActive = offer.is_active && offer.end_date >= today;
            const isExpired = offer.end_date < today;
            
            let statusBadge;
            if (isExpired) {
                statusBadge = '<span class="badge badge-danger">Expired</span>';
            } else if (isActive) {
                statusBadge = '<span class="badge badge-success">Active</span>';
            } else {
                statusBadge = '<span class="badge badge-warning">Inactive</span>';
            }
            
            return `
                <tr>
                    <td>#${offer.id}</td>
                    <td><strong>${offer.product_name}</strong></td>
                    <td><span class="badge badge-primary">${offer.discount_percentage}% OFF</span></td>
                    <td>${formatDate(offer.start_date)}</td>
                    <td>${formatDate(offer.end_date)}</td>
                    <td>${statusBadge}</td>
                    <td>
                        <div class="action-buttons">
                            <button class="btn btn-sm btn-danger" onclick="removeOffer(${offer.id})" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        }).join('');
    } else {
        tbody.innerHTML = '<tr><td colspan="7" class="text-center">No offers found</td></tr>';
    }
}

function filterOffers() {
    const filter = document.getElementById('offer-filter').value;
    const today = new Date().toISOString().split('T')[0];
    let filtered = allOffers;
    
    switch (filter) {
        case 'active':
            filtered = allOffers.filter(o => o. is_active && o.end_date >= today);
            break;
        case 'expired':
            filtered = allOffers.filter(o => o.end_date < today);
            break;
    }
    
    displayOffers(filtered);
}

async function handleAddOffer(event) {
    event.preventDefault();
    
    const data = {
        product_id: document.getElementById('offer_product').value,
        discount_percentage:  document.getElementById('offer_discount').value,
        start_date: document.getElementById('offer_start').value,
        end_date: document.getElementById('offer_end').value
    };
    
    if (data.end_date < data. start_date) {
        showToast('End date must be after start date', 'error');
        return;
    }
    
    showLoading();
    const result = await apiRequest('add_offer', data);
    hideLoading();
    
    if (result. success) {
        showToast(result.message);
        closeModal('add-offer-modal');
        document.getElementById('add-offer-form').reset();
        setDefaultDates();
        loadOffers();
    } else {
        showToast(result.message, 'error');
    }
}

async function removeOffer(id) {
    if (!confirm('Are you sure you want to delete this offer?')) return;
    
    showLoading();
    const result = await apiRequest('remove_offer', { id });
    hideLoading();
    
    if (result.success) {
        showToast(result. message);
        loadOffers();
    } else {
        showToast(result. message, 'error');
    }
}
</script>