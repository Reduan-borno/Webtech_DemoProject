<? php
session_start();

// Get the requested page
$page = $_GET['page'] ?? 'home';
$role = $_SESSION['role'] ?? null;

// Define base path
define('BASE_PATH', __DIR__);

// Check if user is logged in
$isLoggedIn = isset($_SESSION['user_id']);

// Route to appropriate page
if (! $isLoggedIn && ! in_array($page, ['home', 'login', 'register'])) {
    header('Location: index.php?page=login');
    exit;
}

// Role-based access control
$adminPages = ['admin_dashboard', 'employees', 'admin_customers', 'categories', 'stock_logs'];
$employeePages = ['employee_dashboard', 'employee_customers', 'products', 'inventory', 'offers'];
$customerPages = ['customer_dashboard', 'browse_products', 'wishlist', 'orders', 'profile'];

if ($isLoggedIn) {
    if ($role === 'admin' && in_array($page, array_merge($employeePages, $customerPages))) {
        header('Location: index. php?page=admin_dashboard');
        exit;
    } elseif ($role === 'employee' && in_array($page, array_merge($adminPages, $customerPages))) {
        header('Location: index. php?page=employee_dashboard');
        exit;
    } elseif ($role === 'customer' && in_array($page, array_merge($adminPages, $employeePages))) {
        header('Location: index.php? page=customer_dashboard');
        exit;
    }
}
?>
<! DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GadgetGrid - Tech Accessories Store</title>
    <link rel="stylesheet" href="assets/css/style. css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <? php include 'views/layouts/header.php'; ?>
    
    <main class="main-content">
        <?php
        switch ($page) {
            // Public pages
            case 'home':
                include 'views/home.php';
                break;
            case 'login': 
                include 'views/auth/login. php';
                break;
            case 'register':
                include 'views/auth/register.php';
                break;
            
            // Admin pages
            case 'admin_dashboard':
                include 'views/admin/dashboard.php';
                break;
            case 'employees': 
                include 'views/admin/employees.php';
                break;
            case 'admin_customers':
                include 'views/admin/customers.php';
                break;
            case 'categories':
                include 'views/admin/categories.php';
                break;
            case 'stock_logs':
                include 'views/admin/stock_logs.php';
                break;
            
            // Employee pages
            case 'employee_dashboard':
                include 'views/employee/dashboard.php';
                break;
            case 'employee_customers': 
                include 'views/employee/customers. php';
                break;
            case 'products':
                include 'views/employee/products.php';
                break;
            case 'inventory':
                include 'views/employee/inventory.php';
                break;
            case 'offers':
                include 'views/employee/offers.php';
                break;
            
            // Customer pages
            case 'customer_dashboard': 
                include 'views/customer/dashboard. php';
                break;
            case 'browse_products': 
                include 'views/customer/products.php';
                break;
            case 'wishlist': 
                include 'views/customer/wishlist.php';
                break;
            case 'orders': 
                include 'views/customer/orders.php';
                break;
            case 'profile': 
                include 'views/customer/profile. php';
                break;
            
            // Logout
            case 'logout':
                session_destroy();
                header('Location: index. php?page=login');
                exit;
                break;
            
            default: 
                include 'views/404.php';
                break;
        }
        ?>
    </main>
    
    <?php include 'views/layouts/footer.php'; ?>
    
    <script src="assets/js/main.js"></script>
</body>
</html>