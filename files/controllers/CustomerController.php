<? php
require_once __DIR__ . '/../config/database. php';
require_once __DIR__ .  '/../models/User.php';
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/Category.php';
require_once __DIR__ . '/../models/Order.php';
require_once __DIR__ . '/../models/Wishlist.php';
require_once __DIR__ . '/../models/Offer.php';

class CustomerController {
    private $db;
    private $user;
    private $product;
    private $category;
    private $order;
    private $wishlist;
    private $offer;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->user = new User($this->db);
        $this->product = new Product($this->db);
        $this->category = new Category($this->db);
        $this->order = new Order($this->db);
        $this->wishlist = new Wishlist($this->db);
        $this->offer = new Offer($this->db);
    }

    // Get dashboard stats
    public function getDashboardStats($customerId) {
        $orders = $this->order->getByCustomer($customerId);
        $wishlist = $this->wishlist->getByCustomer($customerId);

        return [
            'total_orders' => count($orders),
            'wishlist_items' => count($wishlist)
        ];
    }

    // Product Browsing
    public function getProducts() {
        $products = $this->product->getAll();
        // Add offer info to products
        foreach ($products as &$product) {
            $offer = $this->offer->getByProduct($product['id']);
            if ($offer) {
                $product['offer'] = $offer;
                $product['discounted_price'] = $product['price'] * (1 - $offer['discount_percentage'] / 100);
            }
        }
        return $products;
    }

    public function getProductById($id) {
        $product = $this->product->getById($id);
        if ($product) {
            $offer = $this->offer->getByProduct($id);
            if ($offer) {
                $product['offer'] = $offer;
                $product['discounted_price'] = $product['price'] * (1 - $offer['discount_percentage'] / 100);
            }
        }
        return $product;
    }

    public function getCategories() {
        return $this->category->getAll();
    }

    public function searchProducts($keyword) {
        return $this->product->search($keyword);
    }

    public function getProductsByCategory($categoryId) {
        return $this->product->getByCategory($categoryId);
    }

    // Order History
    public function getOrders($customerId) {
        return $this->order->getByCustomer($customerId);
    }

    public function getOrderDetails($orderId) {
        return $this->order->getOrderDetails($orderId);
    }

    // Place order
    public function placeOrder($customerId, $items) {
        $totalAmount = 0;
        foreach ($items as $item) {
            $product = $this->product->getById($item['product_id']);
            $offer = $this->offer->getByProduct($item['product_id']);
            $price = $offer ? $product['price'] * (1 - $offer['discount_percentage'] / 100) : $product['price'];
            $totalAmount += $price * $item['quantity'];
        }

        $this->order->customer_id = $customerId;
        $this->order->total_amount = $totalAmount;
        $this->order->status = 'pending';

        $orderId = $this->order->create();
        if ($orderId) {
            foreach ($items as $item) {
                $product = $this->product->getById($item['product_id']);
                $offer = $this->offer->getByProduct($item['product_id']);
                $price = $offer ? $product['price'] * (1 - $offer['discount_percentage'] / 100) : $product['price'];
                $this->order->addItem($orderId, $item['product_id'], $item['quantity'], $price);
            }
            return ['success' => true, 'message' => 'Order placed successfully', 'order_id' => $orderId];
        }
        return ['success' => false, 'message' => 'Failed to place order'];
    }

    // Wishlist Management
    public function getWishlist($customerId) {
        return $this->wishlist->getByCustomer($customerId);
    }

    public function addToWishlist($customerId, $productId) {
        if ($this->wishlist->add($customerId, $productId)) {
            return ['success' => true, 'message' => 'Added to wishlist'];
        }
        return ['success' => false, 'message' => 'Failed to add to wishlist'];
    }

    public function removeFromWishlist($customerId, $productId) {
        if ($this->wishlist->remove($customerId, $productId)) {
            return ['success' => true, 'message' => 'Removed from wishlist'];
        }
        return ['success' => false, 'message' => 'Failed to remove from wishlist'];
    }

    public function isInWishlist($customerId, $productId) {
        return $this->wishlist->isInWishlist($customerId, $productId);
    }

    // Profile Management
    public function getProfile($userId) {
        return $this->user->getById($userId);
    }

    public function updateProfile($data) {
        $this->user->id = $data['id'];
        $this->user->full_name = $data['full_name'];
        $this->user->email = $data['email'];
        $this->user->phone = $data['phone'];
        $this->user->address = $data['address'];

        if ($this->user->updateProfile()) {
            return ['success' => true, 'message' => 'Profile updated successfully'];
        }
        return ['success' => false, 'message' => 'Failed to update profile'];
    }

    public function deleteAccount($userId) {
        if ($this->user->delete($userId)) {
            return ['success' => true, 'message' => 'Account deleted successfully'];
        }
        return ['success' => false, 'message' => 'Failed to delete account'];
    }
}
?>