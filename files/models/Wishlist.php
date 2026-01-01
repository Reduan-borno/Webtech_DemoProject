<?php
class Wishlist {
    private $conn;
    private $table = "wishlist";

    public $id;
    public $customer_id;
    public $product_id;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Add to wishlist
    public function add($customerId, $productId) {
        $query = "INSERT IGNORE INTO " . $this->table . " (customer_id, product_id) VALUES (:customer_id, :product_id)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(": customer_id", $customerId);
        $stmt->bindParam(":product_id", $productId);
        return $stmt->execute();
    }

    // Remove from wishlist
    public function remove($customerId, $productId) {
        $query = "DELETE FROM " . $this->table . " WHERE customer_id = :customer_id AND product_id = :product_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(": customer_id", $customerId);
        $stmt->bindParam(":product_id", $productId);
        return $stmt->execute();
    }

    // Get customer wishlist
    public function getByCustomer($customerId) {
        $query = "SELECT w.*, p.name, p.price, p.image, c.name as category_name, i.quantity as stock 
                  FROM " . $this->table . " w 
                  JOIN products p ON w.product_id = p.id 
                  LEFT JOIN categories c ON p.category_id = c.id 
                  LEFT JOIN inventory i ON p.id = i.product_id 
                  WHERE w.customer_id = :customer_id 
                  ORDER BY w.created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":customer_id", $customerId);
        $stmt->execute();
        return $stmt->fetchAll(PDO:: FETCH_ASSOC);
    }

    // Check if product is in wishlist
    public function isInWishlist($customerId, $productId) {
        $query = "SELECT COUNT(*) FROM " . $this->table . " WHERE customer_id = :customer_id AND product_id = :product_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":customer_id", $customerId);
        $stmt->bindParam(":product_id", $productId);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }
}
?>