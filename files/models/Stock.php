<?php
class Stock {
    private $conn;
    private $inventoryTable = "inventory";
    private $logTable = "stock_logs";

    public function __construct($db) {
        $this->conn = $db;
    }

    // Get stock by product
    public function getByProduct($productId) {
        $query = "SELECT * FROM " . $this->inventoryTable .  " WHERE product_id = :product_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":product_id", $productId);
        $stmt->execute();
        return $stmt->fetch(PDO:: FETCH_ASSOC);
    }

    // Stock In
    public function stockIn($productId, $quantity, $employeeId, $notes = '') {
        $this->conn->beginTransaction();
        try {
            // Update inventory
            $query = "UPDATE " .  $this->inventoryTable . " SET quantity = quantity + :quantity WHERE product_id = : product_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":quantity", $quantity);
            $stmt->bindParam(":product_id", $productId);
            $stmt->execute();

            // Log action
            $this->logAction($productId, $employeeId, 'stock_in', $quantity, $notes);

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }

    // Stock Out
    public function stockOut($productId, $quantity, $employeeId, $notes = '') {
        $this->conn->beginTransaction();
        try {
            // Check current stock
            $current = $this->getByProduct($productId);
            if ($current['quantity'] < $quantity) {
                return false; // Insufficient stock
            }

            // Update inventory
            $query = "UPDATE " . $this->inventoryTable . " SET quantity = quantity - :quantity WHERE product_id = : product_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":quantity", $quantity);
            $stmt->bindParam(":product_id", $productId);
            $stmt->execute();

            // Log action
            $this->logAction($productId, $employeeId, 'stock_out', $quantity, $notes);

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }

    // Log stock action
    private function logAction($productId, $employeeId, $action, $quantity, $notes) {
        $query = "INSERT INTO " . $this->logTable . " (product_id, employee_id, action, quantity, notes) 
                  VALUES (: product_id, : employee_id, : action, :quantity, :notes)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":product_id", $productId);
        $stmt->bindParam(":employee_id", $employeeId);
        $stmt->bindParam(":action", $action);
        $stmt->bindParam(":quantity", $quantity);
        $stmt->bindParam(": notes", $notes);
        return $stmt->execute();
    }

    // Get all stock logs (for admin)
    public function getAllLogs() {
        $query = "SELECT sl.*, p.name as product_name, u.full_name as employee_name 
                  FROM " .  $this->logTable . " sl 
                  JOIN products p ON sl.product_id = p. id 
                  JOIN users u ON sl.employee_id = u.id 
                  ORDER BY sl.created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO:: FETCH_ASSOC);
    }

    // Get low stock products
    public function getLowStock() {
        $query = "SELECT i.*, p.name as product_name 
                  FROM " . $this->inventoryTable . " i 
                  JOIN products p ON i. product_id = p.id 
                  WHERE i.quantity <= i.reorder_level";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>