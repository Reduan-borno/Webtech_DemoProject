<div class="page-header">
    <h1><i class="fas fa-heart"></i> My Wishlist</h1>
    <p>Items you've saved for later</p>
</div>

<div id="wishlist-container">
    <div class="text-center">Loading...</div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    loadWishlist();
});

async function loadWishlist() {
    const result = await apiRequest('get_wishlist');
    const container = document.getElementById('wishlist-container');
    
    if (result.success && result.data. length > 0) {
        container. innerHTML = result.data.map(item => `
            <div class="wishlist-item">
                <div class="wishlist-item-image">
                    <i class="fas fa-box"></i>
                </div>
                <div class="wishlist-item-content">
                    <h3>${item.name}</h3>
                    <div class="product-category">${item.category_name || 'General'}</div>
                    <div class="price">$${parseFloat(item.price).toFixed(2)}</div>
                    <div class="product-stock ${item.stock > 0 ? 'available' : 'out'}">
                        ${item.stock > 0 ? `${item.stock} in stock` : 'Out of stock'}
                    </div>
                    <div class="wishlist-item-actions">
                        <button class="btn btn-danger btn-sm" onclick="removeFromWishlist(${item. product_id})">
                            <i class="fas fa-trash"></i> Remove
                        </button>
                    </div>
                </div>
            </div>
        `).join('');
    } else {
        container.innerHTML = `
            <div class="empty-state">
                <i class="fas fa-heart"></i>
                <h3>Your Wishlist is Empty</h3>
                <p>Save items you like by clicking the heart icon on products</p>
                <a href="index.php?page=browse_products" class="btn btn-primary">
                    <i class="fas fa-shopping-bag"></i> Browse Products
                </a>
            </div>
        `;
    }
}

async function removeFromWishlist(productId) {
    if (! confirm('Remove this item from your wishlist?')) return;
    
    showLoading();
    const result = await apiRequest('remove_from_wishlist', { product_id: productId });
    hideLoading();
    
    if (result. success) {
        showToast(result.message);
        loadWishlist();
    } else {
        showToast(result.message, 'error');
    }
}
</script>