<? php
$currentPage = $_GET['page'] ??  'home';
$isLoggedIn = isset($_SESSION['user_id']);
$userRole = $_SESSION['role'] ?? null;
$userName = $_SESSION['full_name'] ?? '';
$userInitial = $userName ?  strtoupper(substr($userName, 0, 1)) : 'U';
?>

<header class="header">
    <div class="header-container">
        <a href="index.php" class="logo">
            <i class="fas fa-microchip"></i>
            <span>GadgetGrid</span>
        </a>

        <button class="menu-toggle" onclick="toggleMobileMenu()">
            <i class="fas fa-bars"></i>
        </button>

        <nav class="nav-menu" id="navMenu">
            <? php if (! $isLoggedIn): ?>
                <a href="index. php?page=home" class="<? = $currentPage === 'home' ?  'active' :  '' ?>">
                    <i class="fas fa-home"></i> Home
                </a>
                <a href="index.php?page=login" class="<?= $currentPage === 'login' ? 'active' : '' ?>">
                    <i class="fas fa-sign-in-alt"></i> Login
                </a>
                <a href="index.php? page=register" class="<?= $currentPage === 'register' ? 'active' : '' ?>">
                    <i class="fas fa-user-plus"></i> Register
                </a>
            <?php elseif ($userRole === 'admin'): ?>
                <a href="index. php?page=admin_dashboard" class="<? = $currentPage === 'admin_dashboard' ? 'active' : '' ?>">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
                <a href="index.php? page=employees" class="<?= $currentPage === 'employees' ? 'active' : '' ?>">
                    <i class="fas fa-user-tie"></i> Employees
                </a>
                <a href="index.php? page=admin_customers" class="<?= $currentPage === 'admin_customers' ? 'active' : '' ?>">
                    <i class="fas fa-users"></i> Customers
                </a>
                <a href="index.php? page=categories" class="<?= $currentPage === 'categories' ? 'active' : '' ?>">
                    <i class="fas fa-tags"></i> Categories
                </a>
                <a href="index.php?page=stock_logs" class="<?= $currentPage === 'stock_logs' ? 'active' : '' ?>">
                    <i class="fas fa-clipboard-list"></i> Stock Logs
                </a>
            <?php elseif ($userRole === 'employee'): ?>
                <a href="index.php?page=employee_dashboard" class="<?= $currentPage === 'employee_dashboard' ? 'active' : '' ?>">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
                <a href="index.php?page=employee_customers" class="<?= $currentPage === 'employee_customers' ? 'active' : '' ?>">
                    <i class="fas fa-users"></i> Customers
                </a>
                <a href="index.php? page=products" class="<?= $currentPage === 'products' ? 'active' : '' ?>">
                    <i class="fas fa-box"></i> Products
                </a>
                <a href="index.php?page=inventory" class="<?= $currentPage === 'inventory' ? 'active' : '' ?>">
                    <i class="fas fa-warehouse"></i> Inventory
                </a>
                <a href="index.php?page=offers" class="<?= $currentPage === 'offers' ? 'active' : '' ?>">
                    <i class="fas fa-percent"></i> Offers
                </a>
            <?php elseif ($userRole === 'customer'): ?>
                <a href="index.php?page=customer_dashboard" class="<? = $currentPage === 'customer_dashboard' ? 'active' : '' ?>">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
                <a href="index.php?page=browse_products" class="<?= $currentPage === 'browse_products' ? 'active' : '' ?>">
                    <i class="fas fa-shopping-bag"></i> Products
                </a>
                <a href="index.php?page=wishlist" class="<? = $currentPage === 'wishlist' ? 'active' : '' ?>">
                    <i class="fas fa-heart"></i> Wishlist
                </a>
                <a href="index.php?page=orders" class="<?= $currentPage === 'orders' ? 'active' : '' ?>">
                    <i class="fas fa-receipt"></i> Orders
                </a>
                <a href="index.php?page=profile" class="<? = $currentPage === 'profile' ?  'active' :  '' ?>">
                    <i class="fas fa-user"></i> Profile
                </a>
            <?php endif; ?>
        </nav>

        <? php if ($isLoggedIn): ?>
        <div class="user-menu">
            <div class="user-info">
                <div class="avatar"><?= $userInitial ? ></div>
                <div>
                    <div class="user-name"><?= htmlspecialchars($userName) ?></div>
                    <div class="user-role"><?= $userRole ?></div>
                </div>
            </div>
            <button class="btn-logout" onclick="handleLogout()">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </button>
        </div>
        <?php endif; ?>
    </div>
</header>