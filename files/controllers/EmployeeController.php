<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/Category.php';
require_once __DIR__ . '/../models/Stock.php';
require_once __DIR__ . '/../models/Offer.php';

class EmployeeController {
    private $db;
    private $user;
    private $product;
    private $category;
    private $stock;
    private $offer;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->user = new User($this->db);
        $this->product = new Product($this->db);
        $this->category = new Category($this->db);
        $this->stock = new Stock($this->db);
        $this->offer = new Offer($this->db);
    }

    // Get dashboard stats
    public function getDashboardStats() {
        $products = $this->product->getAll();
        $customers = $this->user->getByRole('customer');
        $lowStock = $this->stock->getLowStock();
        $offers = $this->offer->getActive();

        return [
            'total_products' => count($products),
            'total_customers' => count($customers),
            'low_stock_count' => count($lowStock),
            'active_offers' => count($offers)
        ];
    }

    // Customer Management
    public function getCustomers() {
        return $this->user->getByRole('customer');
    }

    public function addCustomer($data) {
        $this->user->username = $data['username'];
        $this->user->email = $data['email'];
        $this->user->password = $data['password'];
        $this->user->full_name = $data['full_name'];
        $this->user->phone = $data['phone'] ?? '';
        $this->user->address = $data['address'] ?? '';
        $this->user->role = 'customer';
        $this->user->status = 'approved';

        if ($this->user->create()) {
            return ['success' => true, 'message' => 'Customer added successfully'];
        }
        return ['success' => false, 'message' => 'Failed to add customer'];
    }

    public function removeCustomer($id) {
        if ($this->user->delete($id)) {
            return ['success' => true, 'message' => 'Customer removed successfully'];
        }
        return ['success' => false, 'message' => 'Failed to remove customer'];
    }

    // Product Management
    public function getProducts() {
        return $this->product->getAll();
    }

    public function getCategories() {
        return $this->category->getAll();
    }

    public function addProduct($data) {
        $this->product->name = $data['name'];
        $this->product->description = $data['description'];
        $this->product->price = $data['price'];
        $this->product->category_id = $data['category_id'];
        $this->product->specifications = json_encode($data['specifications'] ?? []);
        $this->product->image = $data['image'] ?? 'product_default.png';

        if ($this->product->create()) {
            return ['success' => true, 'message' => 'Product added successfully'];
        }
        return ['success' => false, 'message' => 'Failed to add product'];
    }

    public function updateProduct($data) {
        $this->product->id = $data['id'];
        $this->product->name = $data['name'];
        $this->product->description = $data['description'];
        $this->product->price = $data['price'];
        $this->product->category_id = $data['category_id'];
        $this->product->specifications = json_encode($data['specifications'] ?? []);

        if ($this->product->update()) {
            return ['success' => true, 'message' => 'Product updated successfully'];
        }
        return ['success' => false, 'message' => 'Failed to update product'];
    }

    public function removeProduct($id) {
        if ($this->product->delete($id)) {
            return ['success' => true, 'message' => 'Product removed successfully'];
        }
        return ['success' => false, 'message' => 'Failed to remove product'];
    }

    // Price Management
    public function updatePrice($productId, $price) {
        if ($this->product->updatePrice($productId, $price)) {
            return ['success' => true, 'message' => 'Price updated successfully'];
        }
        return ['success' => false, 'message' => 'Failed to update price'];
    }

    // Inventory/Stock Management
    public function stockIn($productId, $quantity, $employeeId, $notes = '') {
        if ($this->stock->stockIn($productId, $quantity, $employeeId, $notes)) {
            return ['success' => true, 'message' => 'Stock added successfully'];
        }
        return ['success' => false, 'message' => 'Failed to add stock'];
    }

    public function stockOut($productId, $quantity, $employeeId, $notes = '') {
        if ($this->stock->stockOut($productId, $quantity, $employeeId, $notes)) {
            return ['success' => true, 'message' => 'Stock removed successfully'];
        }
        return ['success' => false, 'message' => 'Insufficient stock or failed to remove'];
    }

    public function getLowStock() {
        return $this->stock->getLowStock();
    }

    // Offer Management
    public function getOffers() {
        return $this->offer->getAll();
    }

    public function addOffer($data) {
        $this->offer->product_id = $data['product_id'];
        $this->offer->discount_percentage = $data['discount_percentage'];
        $this->offer->start_date = $data['start_date'];
        $this->offer->end_date = $data['end_date'];
        $this->offer->is_active = 1;
        $this->offer->created_by = $data['created_by'];

        if ($this->offer->create()) {
            return ['success' => true, 'message' => 'Offer added successfully'];
        }
        return ['success' => false, 'message' => 'Failed to add offer'];
    }

    public function removeOffer($id) {
        if ($this->offer->delete($id)) {
            return ['success' => true, 'message' => 'Offer removed successfully'];
        }
        return ['success' => false, 'message' => 'Failed to remove offer'];
    }
}
?>