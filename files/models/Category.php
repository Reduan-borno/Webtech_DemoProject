<?php
class Category {
    private $conn;
    private $table = "categories";

    public $id;
    public $name;
    public $description;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Get all categories
    public function getAll() {
        $query = "SELECT * FROM " . $this->table . " ORDER BY name";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO:: FETCH_ASSOC);
    }

    // Get category by ID
    public function getById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetch(PDO:: FETCH_ASSOC);
    }

    // Create category
    public function create() {
        $query = "INSERT INTO " . $this->table . " SET name=:name, description=:description";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":description", $this->description);
        return $stmt->execute();
    }

    // Update category
    public function update() {
        $query = "UPDATE " . $this->table . " SET name=:name, description=:description WHERE id=:id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(": description", $this->description);
        $stmt->bindParam(":id", $this->id);
        return $stmt->execute();
    }

    // Delete category
    public function delete($id) {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }

    // Get category with product count
    public function getWithProductCount() {
        $query = "SELECT c.*, COUNT(p.id) as product_count 
                  FROM " . $this->table . " c 
                  LEFT JOIN products p ON c.id = p.category_id 
                  GROUP BY c.id 
                  ORDER BY c.name";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>