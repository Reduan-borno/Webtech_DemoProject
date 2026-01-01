<div class="page-header">
    <h1><i class="fas fa-shopping-bag"></i> Browse Products</h1>
    <p>Discover the latest tech accessories</p>
</div>

<div class="search-box">
    <div class="search-input">
        <i class="fas fa-search"></i>
        <input type="text" id="product-search" placeholder="Search products..." onkeyup="searchProducts()">
    </div>
    <select id="category-filter" class="filter-select" onchange="filterByCategory()">
        <option value="">All Categories</option>
    </select>
    <select id="sort-filter" class="filter-select" onchange="sortProducts()">
        <option value="">Sort By</option>
        <option value="price_asc">Price:  Low to High</option>
        <option value="price_desc">Price: High to Low</option>
        <option value="name_asc">Name: A-Z</option>
        <option value="name_desc">Name: Z-A</option>
    </select>
</div>

<div class="products-grid" id="products-container">
    <div class="text-center" style="grid-column: 1/-1;">Loading products...</div>
</div>

<!-- Product Detail Modal -->
<div class="modal-overlay" id="product-detail-modal">
    <div class="modal" style="max-width: 600px;">
        <div class="modal-header">
            <h3 id="modal-product-name">Product Details</h3>
            <button class="modal-close" onclick="closeModal('product-detail-modal')">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body" id="product-detail-content">
            <!-- Product details will be loaded here -->
        </div>
    </div>
</div>

<script>
let allProducts = [];
let categories = [];

document.addEventListener('DOMContentLoaded', function() {
    loadProducts();
    loadCategories();
});

async function loadProducts() {
    const result = await apiRequest('get_customer_products');
    const container = document.getElementById('products-container');
    
    if (result.success && result.data.length > 0) {
        allProducts = result.data;
        displayProducts(result.data);
    } else {
        container.innerHTML = '<div class="empty-state" style="grid-column: 1/-1;"><i class="fas fa-box-open"></i><h3>No Products Available</h3><p>Check back later for new products!</p></div>';
    }
}

async function loadCategories() {
    const result = await apiRequest('get_categories');
    if (result.success) {
        categories = result.data;
        const options = categories.map(cat => `<option value="${cat.id}">${cat.name}</option>`).join('');
        document.getElementById('category-filter').innerHTML = '<option value="">All Categories</option>' + options;
    }
}

function displayProducts(products) {
    const container = document.getElementById('products-container');
    
    if (products. length > 0) {
        container.innerHTML = products.map(product => {
            const hasOffer = product. offer && product.offer.discount_percentage;
            const discountedPrice = hasOffer ? (product. price * (1 - product.offer. discount_percentage / 100)).toFixed(2) : null;
            const stockStatus = product.stock > 10 ? 'available' : product.stock > 0 ? 'low' : 'out';
            const stockText = product.stock > 0 ? `${product.stock} in stock` : 'Out of stock';
            
            return `
                <div class="product-card">
                    <div class="product-image">
                        <i class="fas fa-box"></i>
                        ${hasOffer ? `<span class="product-offer">${product.offer.discount_percentage}% OFF</span>` : ''}
                    </div>
                    <div class="product-content">
                        <div class="product-category">${product.category_name || 'General'}</div>
                        <h3 class="product-title">${product.name}</h3>
                        <div class="product-price">
                            ${hasOffer ? `
                                <span class="current">$${discountedPrice}</span>
                                <span class="original">$${parseFloat(product.price).toFixed(2)}</span>
                            ` : `
                                <span class="current">$${parseFloat(product.price).toFixed(2)}</span>
                            `}
                        </div>
                        <div class="product-stock ${stockStatus}">${stockText}</div>
                        <div class="product-actions">
                            <button class="btn btn-primary btn-sm" onclick="viewProduct(${product. id})">
                                <i class="fas fa-eye"></i> View
                            </button>
                            <button class="btn btn-danger btn-sm" onclick="addToWishlist(${product.id})">
                                <i class="fas fa-heart"></i>
                            </button>
                        </div>
                    </div>
                </div>
            `;
        }).join('');
    } else {
        container.innerHTML = '<div class="empty-state" style="grid-column: 1/-1;"><i class="fas fa-search"></i><h3>No Products Found</h3><p>Try adjusting your search or filters</p></div>';
    }
}

function searchProducts() {
    const searchTerm = document. getElementById('product-search').value.toLowerCase();
    const filtered = allProducts.filter(p => 
        p.name.toLowerCase().includes(searchTerm) ||
        (p.description && p.description. toLowerCase().includes(searchTerm)) ||
        (p.category_name && p.category_name.toLowerCase().includes(searchTerm))
    );
    displayProducts(filtered);
}

function filterByCategory() {
    const categoryId = document.getElementById('category-filter').value;
    if (categoryId) {
        const filtered = allProducts.filter(p => p.category_id == categoryId);
        displayProducts(filtered);
    } else {
        displayProducts(allProducts);
    }
}

function sortProducts() {
    const sortBy = document.getElementById('sort-filter').value;
    let sorted = [... allProducts];
    
    switch (sortBy) {
        case 'price_asc':
            sorted.sort((a, b) => a.price - b. price);
            break;
        case 'price_desc': 
            sorted.sort((a, b) => b.price - a.price);
            break;
        case 'name_asc':
            sorted.sort((a, b) => a.name.localeCompare(b. name));
            break;
        case 'name_desc': 
            sorted.sort((a, b) => b.name. localeCompare(a.name));
            break;
    }
    
    displayProducts(sorted);
}

async function viewProduct(id) {
    const result = await apiRequest('get_product_detail', { id });
    if (result. success) {
        const product = result.data;
        const hasOffer = product.offer && product. offer.discount_percentage;
        const discountedPrice = hasOffer ? (product.price * (1 - product.offer.discount_percentage / 100)).toFixed(2) : null;
        
        document.getElementById('modal-product-name').textContent = product.name;
        document. getElementById('product-detail-content').innerHTML = `
            <div style="text-align: center; padding: 20px; background: var(--gray-100); border-radius: var(--border-radius); margin-bottom: 20px;">
                <i class="fas fa-box" style="font-size: 5rem; color: var(--gray-400);"></i>
            </div>
            <div class="product-category" style="margin-bottom: 10px;">${product.category_name || 'General'}</div>
            <div class="product-price" style="margin-bottom: 15px;">
                ${hasOffer ? `
                    <span class="current" style="font-size: 1.5rem;">$${discountedPrice}</span>
                    <span class="original">$${parseFloat(product.price).toFixed(2)}</span>
                    <span class="badge badge-danger" style="margin-left: 10px;">${product.offer. discount_percentage}% OFF</span>
                ` : `
                    <span class="current" style="font-size:  1.5rem;">$${parseFloat(product.price).toFixed(2)}</span>
                `}
            </div>
            <p style="color: var(--gray-600); margin-bottom: 20px;">${product.description || 'No description available.'}</p>
            <div style="margin-bottom: 20px;">
                <strong>Availability:</strong> 
                <span class="badge ${product.stock > 0 ? 'badge-success' : 'badge-danger'}">
                    ${product.stock > 0 ? `${product.stock} in stock` : 'Out of stock'}
                </span>
            </div>
            <div class="product-actions" style="justify-content: center;">
                <button class="btn btn-danger" onclick="addToWishlist(${product.id})">
                    <i class="fas fa-heart"></i> Add to Wishlist
                </button>
            </div>
        `;
        openModal('product-detail-modal');
    }
}

async function addToWishlist(productId) {
    showLoading();
    const result = await apiRequest('add_to_wishlist', { product_id: productId });
    hideLoading();
    
    if (result.success) {
        showToast(result.message);
    } else {
        showToast(result.message, 'error');
    }
}
</script>