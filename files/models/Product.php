<? php
class Product {
    private $conn;
    private $table = "products";

    public $id;
    public $name;
    public $description;
    public $price;
    public $category_id;
    public $specifications;
    public $image;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Get all products with category and stock info
    public function getAll() {
        $query = "SELECT p.*, c.name as category_name, i.quantity as stock 
                  FROM " . $this->table . " p 
                  LEFT JOIN categories c ON p.category_id = c. id 
                  LEFT JOIN inventory i ON p.id = i.product_id 
                  ORDER BY p.created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO:: FETCH_ASSOC);
    }

    // Get product by ID
    public function getById($id) {
        $query = "SELECT p.*, c.name as category_name, i.quantity as stock 
                  FROM " . $this->table . " p 
                  LEFT JOIN categories c ON p. category_id = c.id 
                  LEFT JOIN inventory i ON p.id = i.product_id 
                  WHERE p.id = : id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(": id", $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Create product
    public function create() {
        $query = "INSERT INTO " . $this->table . " 
                  SET name=:name, description=:description, price=:price, 
                      category_id=:category_id, specifications=: specifications, image=:image";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(": price", $this->price);
        $stmt->bindParam(":category_id", $this->category_id);
        $stmt->bindParam(":specifications", $this->specifications);
        $stmt->bindParam(":image", $this->image);

        if ($stmt->execute()) {
            $productId = $this->conn->lastInsertId();
            // Create inventory record
            $invQuery = "INSERT INTO inventory (product_id, quantity) VALUES (:product_id, 0)";
            $invStmt = $this->conn->prepare($invQuery);
            $invStmt->bindParam(":product_id", $productId);
            $invStmt->execute();
            return $productId;
        }
        return false;
    }

    // Update product
    public function update() {
        $query = "UPDATE " .  $this->table .  " 
                  SET name=:name, description=:description, price=:price, 
                      category_id=: category_id, specifications=:specifications 
                  WHERE id=:id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(": description", $this->description);
        $stmt->bindParam(":price", $this->price);
        $stmt->bindParam(":category_id", $this->category_id);
        $stmt->bindParam(":specifications", $this->specifications);
        $stmt->bindParam(":id", $this->id);

        return $stmt->execute();
    }

    // Update price
    public function updatePrice($id, $price) {
        $query = "UPDATE " . $this->table . " SET price = :price WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":price", $price);
        $stmt->bindParam(": id", $id);
        return $stmt->execute();
    }

    // Delete product
    public function delete($id) {
        $query = "DELETE FROM " .  $this->table .  " WHERE id = : id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(": id", $id);
        return $stmt->execute();
    }

    // Search products
    public function search($keyword) {
        $query = "SELECT p.*, c.name as category_name, i.quantity as stock 
                  FROM " . $this->table . " p 
                  LEFT JOIN categories c ON p. category_id = c.id 
                  LEFT JOIN inventory i ON p.id = i.product_id 
                  WHERE p.name LIKE :keyword OR c. name LIKE :keyword 
                  ORDER BY p.name";
        $stmt = $this->conn->prepare($query);
        $keyword = "%{$keyword}%";
        $stmt->bindParam(":keyword", $keyword);
        $stmt->execute();
        return $stmt->fetchAll(PDO:: FETCH_ASSOC);
    }

    // Get products by category
    public function getByCategory($categoryId) {
        $query = "SELECT p.*, c.name as category_name, i.quantity as stock 
                  FROM " . $this->table . " p 
                  LEFT JOIN categories c ON p.category_id = c.id 
                  LEFT JOIN inventory i ON p. id = i.product_id 
                  WHERE p.category_id = :category_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":category_id", $categoryId);
        $stmt->execute();
        return $stmt->fetchAll(PDO:: FETCH_ASSOC);
    }

    // Get products with active offers
    public function getWithOffers() {
        $query = "SELECT p.*, c.name as category_name, i.quantity as stock, 
                         o.discount_percentage, o.end_date 
                  FROM " . $this->table . " p 
                  LEFT JOIN categories c ON p.category_id = c.id 
                  LEFT JOIN inventory i ON p. id = i.product_id 
                  INNER JOIN offers o ON p.id = o.product_id 
                  WHERE o.is_active = 1 AND o.end_date >= CURDATE()";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>