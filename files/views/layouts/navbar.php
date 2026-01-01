<? php
// This file can be used for additional navigation components
// Currently, the main navigation is in header.php

function getNavItems($role) {
    $items = [];
    
    switch ($role) {
        case 'admin':
            $items = [
                ['page' => 'admin_dashboard', 'icon' => 'fa-tachometer-alt', 'label' => 'Dashboard'],
                ['page' => 'employees', 'icon' => 'fa-user-tie', 'label' => 'Employees'],
                ['page' => 'admin_customers', 'icon' => 'fa-users', 'label' => 'Customers'],
                ['page' => 'categories', 'icon' => 'fa-tags', 'label' => 'Categories'],
                ['page' => 'stock_logs', 'icon' => 'fa-clipboard-list', 'label' => 'Stock Logs'],
            ];
            break;
        case 'employee':
            $items = [
                ['page' => 'employee_dashboard', 'icon' => 'fa-tachometer-alt', 'label' => 'Dashboard'],
                ['page' => 'employee_customers', 'icon' => 'fa-users', 'label' => 'Customers'],
                ['page' => 'products', 'icon' => 'fa-box', 'label' => 'Products'],
                ['page' => 'inventory', 'icon' => 'fa-warehouse', 'label' => 'Inventory'],
                ['page' => 'offers', 'icon' => 'fa-percent', 'label' => 'Offers'],
            ];
            break;
        case 'customer':
            $items = [
                ['page' => 'customer_dashboard', 'icon' => 'fa-tachometer-alt', 'label' => 'Dashboard'],
                ['page' => 'browse_products', 'icon' => 'fa-shopping-bag', 'label' => 'Products'],
                ['page' => 'wishlist', 'icon' => 'fa-heart', 'label' => 'Wishlist'],
                ['page' => 'orders', 'icon' => 'fa-receipt', 'label' => 'Orders'],
                ['page' => 'profile', 'icon' => 'fa-user', 'label' => 'Profile'],
            ];
            break;
    }
    
    return $items;
}
?>