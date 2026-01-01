<?php
class Offer {
    private $conn;
    private $table = "offers";

    public $id;
    public $product_id;
    public $discount_percentage;
    public $start_date;
    public $end_date;
    public $is_active;
    public $created_by;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Create offer
    public function create() {
        $query = "INSERT INTO " . $this->table . " 
                  SET product_id=:product_id, discount_percentage=:discount_percentage, 
                      start_date=:start_date, end_date=: end_date, is_active=:is_active, 
                      created_by=:created_by";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":product_id", $this->product_id);
        $stmt->bindParam(":discount_percentage", $this->discount_percentage);
        $stmt->bindParam(":start_date", $this->start_date);
        $stmt->bindParam(":end_date", $this->end_date);
        $stmt->bindParam(":is_active", $this->is_active);
        $stmt->bindParam(":created_by", $this->created_by);
        return $stmt->execute();
    }

    // Get all offers
    public function getAll() {
        $query = "SELECT o.*, p.name as product_name 
                  FROM " . $this->table . " o 
                  JOIN products p ON o.product_id = p.id 
                  ORDER BY o.created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get active offers
    public function getActive() {
        $query = "SELECT o.*, p.name as product_name, p.price 
                  FROM " .  $this->table .  " o 
                  JOIN products p ON o.product_id = p.id 
                  WHERE o.is_active = 1 AND o.end_date >= CURDATE()";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO:: FETCH_ASSOC);
    }

    // Update offer
    public function update() {
        $query = "UPDATE " . $this->table . " 
                  SET discount_percentage=:discount_percentage, start_date=:start_date, 
                      end_date=:end_date, is_active=: is_active 
                  WHERE id=:id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":discount_percentage", $this->discount_percentage);
        $stmt->bindParam(":start_date", $this->start_date);
        $stmt->bindParam(":end_date", $this->end_date);
        $stmt->bindParam(":is_active", $this->is_active);
        $stmt->bindParam(":id", $this->id);
        return $stmt->execute();
    }

    // Delete offer
    public function delete($id) {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }

    // Get offer for product
    public function getByProduct($productId) {
        $query = "SELECT * FROM " . $this->table . " 
                  WHERE product_id = :product_id AND is_active = 1 AND end_date >= CURDATE()";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":product_id", $productId);
        $stmt->execute();
        return $stmt->fetch(PDO:: FETCH_ASSOC);
    }
}
?>