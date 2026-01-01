<? php
require_once __DIR__ . '/../config/database. php';
require_once __DIR__ .  '/../models/User.php';
require_once __DIR__ . '/../models/Category.php';
require_once __DIR__ . '/../models/Stock.php';
require_once __DIR__ . '/../models/Product.php';

class AdminController {
    private $db;
    private $user;
    private $category;
    private $stock;
    private $product;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->user = new User($this->db);
        $this->category = new Category($this->db);
        $this->stock = new Stock($this->db);
        $this->product = new Product($this->db);
    }

    // Get dashboard stats
    public function getDashboardStats() {
        $employees = $this->user->getByRole('employee');
        $customers = $this->user->getByRole('customer');
        $pendingEmployees = $this->user->getPendingEmployees();
        $categories = $this->category->getAll();
        $products = $this->product->getAll();
        $lowStock = $this->stock->getLowStock();

        return [
            'total_employees' => count($employees),
            'total_customers' => count($customers),
            'pending_approvals' => count($pendingEmployees),
            'total_categories' => count($categories),
            'total_products' => count($products),
            'low_stock_count' => count($lowStock)
        ];
    }

    // Get pending employees
    public function getPendingEmployees() {
        return $this->user->getPendingEmployees();
    }

    // Approve employee
    public function approveEmployee($employeeId) {
        if ($this->user->updateStatus($employeeId, 'approved')) {
            return ['success' => true, 'message' => 'Employee approved successfully'];
        }
        return ['success' => false, 'message' => 'Failed to approve employee'];
    }

    // Reject employee
    public function rejectEmployee($employeeId) {
        if ($this->user->updateStatus($employeeId, 'rejected')) {
            return ['success' => true, 'message' => 'Employee rejected'];
        }
        return ['success' => false, 'message' => 'Failed to reject employee'];
    }

    // Get all employees
    public function getAllEmployees() {
        return $this->user->getByRole('employee');
    }

    // Get all customers
    public function getAllCustomers() {
        return $this->user->getByRole('customer');
    }

    // Category Management
    public function getCategories() {
        return $this->category->getWithProductCount();
    }

    public function addCategory($name, $description) {
        $this->category->name = $name;
        $this->category->description = $description;
        
        if ($this->category->create()) {
            return ['success' => true, 'message' => 'Category added successfully'];
        }
        return ['success' => false, 'message' => 'Failed to add category'];
    }

    public function updateCategory($id, $name, $description) {
        $this->category->id = $id;
        $this->category->name = $name;
        $this->category->description = $description;
        
        if ($this->category->update()) {
            return ['success' => true, 'message' => 'Category updated successfully'];
        }
        return ['success' => false, 'message' => 'Failed to update category'];
    }

    public function deleteCategory($id) {
        if ($this->category->delete($id)) {
            return ['success' => true, 'message' => 'Category deleted successfully'];
        }
        return ['success' => false, 'message' => 'Failed to delete category'];
    }

    // Get stock logs
    public function getStockLogs() {
        return $this->stock->getAllLogs();
    }

    // Delete user
    public function deleteUser($id) {
        if ($this->user->delete($id)) {
            return ['success' => true, 'message' => 'User deleted successfully'];
        }
        return ['success' => false, 'message' => 'Failed to delete user'];
    }
}
?>