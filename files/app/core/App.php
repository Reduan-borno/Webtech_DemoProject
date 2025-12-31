<?php
/**
 * App Class
 * Main application router
 * Parses URL and loads appropriate controller/method
 */

class App {
    protected $controller = 'AuthController';
    protected $method = 'login';
    protected $params = [];

    public function __construct() {
        Session::start();
        $url = $this->parseUrl();

        // Define routes
        $routes = [
            '' => ['AuthController', 'login'],
            'login' => ['AuthController', 'login'],
            'register' => ['AuthController', 'register'],
            'logout' => ['AuthController', 'logout'],
            'reset-password' => ['AuthController', 'resetPassword'],
            'dashboard' => ['DashboardController', 'index'],
            
            // Profile routes
            'profile' => ['DashboardController', 'profile'],
            'profile/edit' => ['DashboardController', 'editProfile'],
            'profile/change-password' => ['DashboardController', 'changePassword'],
            'profile/delete' => ['DashboardController', 'deleteProfile'],
            
            // Admin routes
            'admin/dashboard' => ['AdminController', 'dashboard'],
            'admin/users' => ['AdminController', 'users'],
            'admin/employees' => ['AdminController', 'employees'],
            'admin/categories' => ['AdminController', 'categories'],
            'admin/logs' => ['AdminController', 'logs'],
            'admin/approve-employee' => ['AdminController', 'approveEmployee'],
            'admin/reject-employee' => ['AdminController', 'rejectEmployee'],
            'admin/add-category' => ['AdminController', 'addCategory'],
            'admin/edit-category' => ['AdminController', 'editCategory'],
            'admin/delete-category' => ['AdminController', 'deleteCategory'],
            
            // Employee routes
            'employee/dashboard' => ['EmployeeController', 'dashboard'],
            'employee/products' => ['EmployeeController', 'products'],
            'employee/add-product' => ['EmployeeController', 'addProduct'],
            'employee/edit-product' => ['EmployeeController', 'editProduct'],
            'employee/delete-product' => ['EmployeeController', 'deleteProduct'],
            'employee/stock' => ['EmployeeController', 'stock'],
            'employee/update-stock' => ['EmployeeController', 'updateStock'],
            'employee/customers' => ['EmployeeController', 'customers'],
            'employee/add-customer' => ['EmployeeController', 'addCustomer'],
            'employee/delete-customer' => ['EmployeeController', 'deleteCustomer'],
            'employee/offers' => ['EmployeeController', 'offers'],
            'employee/add-offer' => ['EmployeeController', 'addOffer'],
            'employee/delete-offer' => ['EmployeeController', 'deleteOffer'],
            'employee/update-price' => ['EmployeeController', 'updatePrice'],
            
            // Customer routes
            'customer/dashboard' => ['CustomerController', 'dashboard'],
            'customer/products' => ['CustomerController', 'products'],
            'customer/product' => ['CustomerController', 'productDetail'],
            'customer/search' => ['CustomerController', 'search'],
            'customer/wishlist' => ['CustomerController', 'wishlist'],
            'customer/add-to-wishlist' => ['CustomerController', 'addToWishlist'],
            'customer/remove-from-wishlist' => ['CustomerController', 'removeFromWishlist'],
            'customer/orders' => ['CustomerController', 'orders'],
            'customer/place-order' => ['CustomerController', 'placeOrder'],
            
            // Product routes (public)
            'products' => ['ProductController', 'index'],
            'products/category' => ['ProductController', 'byCategory'],
            'products/detail' => ['ProductController', 'detail'],
        ];

        // Build the route from URL
        $route = implode('/', $url);
        
        // Check if route exists
        if (isset($routes[$route])) {
            $this->controller = $routes[$route][0];
            $this->method = $routes[$route][1];
        } elseif (isset($url[0]) && isset($routes[$url[0]])) {
            $this->controller = $routes[$url[0]][0];
            $this->method = $routes[$url[0]][1];
            array_shift($url);
            $this->params = $url;
        } else {
            // Try to match partial route
            $partialRoute = isset($url[0]) ? $url[0] :  '';
            if (isset($url[1])) {
                $partialRoute .= '/' . $url[1];
            }
            
            if (isset($routes[$partialRoute])) {
                $this->controller = $routes[$partialRoute][0];
                $this->method = $routes[$partialRoute][1];
                array_shift($url);
                array_shift($url);
                $this->params = $url;
            }
        }

        // Load controller
        $controllerFile = APP_ROOT . '/app/controllers/' .  $this->controller .  '.php';
        if (file_exists($controllerFile)) {
            require_once $controllerFile;
            $this->controller = new $this->controller();
        } else {
            die('Controller not found:  ' . $this->controller);
        }

        // Check if method exists
        if (method_exists($this->controller, $this->method)) {
            call_user_func_array([$this->controller, $this->method], $this->params);
        } else {
            die('Method not found:  ' . $this->method);
        }
    }

    /**
     * Parse URL into array
     */
    protected function parseUrl() {
        if (isset($_GET['url'])) {
            return explode('/', filter_var(rtrim($_GET['url'], '/'), FILTER_SANITIZE_URL));
        }
        return [];
    }
}