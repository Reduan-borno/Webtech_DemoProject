<?php
/**
 * StockLog Model
 * Handles stock movement logging
 */

class StockLog {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    /**
     * Create stock log entry
     */
    public function create($data) {
        $this->db->query('INSERT INTO stock_logs (product_id, user_id, type, quantity, previous_stock, new_stock, notes) 
                          VALUES (:product_id, :user_id, :type, :quantity, :previous_stock, :new_stock, :notes)');
        $this->db->bind(':product_id', $data['product_id']);
        $this->db->bind(':user_id', $data['user_id']);
        $this->db->bind(':type', $data['type']);
        $this->db->bind(':quantity', $data['quantity']);
        $this->db->bind(': previous_stock', $data['previous_stock']);
        $this->db->bind(': new_stock', $data['new_stock']);
        $this->db->bind(': notes', $data['notes'] ?? null);
        
        if ($this->db->execute()) {
            return $this->db->lastInsertId();
        }
        return false;
    }

    /**
     * Get all logs
     */
    public function getAll($limit = 100) {
        $this->db->query('SELECT sl.*, p.name as product_name, u.full_name as user_name 
                          FROM stock_logs sl 
                          LEFT JOIN products p ON sl.product_id = p.id 
                          LEFT JOIN users u ON sl.user_id = u. id 
                          ORDER BY sl.created_at DESC 
                          LIMIT : limit');
        $this->db->bind(':limit', $limit, PDO::PARAM_INT);
        return $this->db->resultSet();
    }

    /**
     * Get logs by product
     */
    public function getByProduct($productId) {
        $this->db->query('SELECT sl.*, u.full_name as user_name 
                          FROM stock_logs sl 
                          LEFT JOIN users u ON sl.user_id = u.id 
                          WHERE sl.product_id = :product_id 
                          ORDER BY sl.created_at DESC');
        $this->db->bind(':product_id', $productId);
        return $this->db->resultSet();
    }

    /**
     * Get logs by type
     */
    public function getByType($type) {
        $this->db->query('SELECT sl.*, p.name as product_name, u.full_name as user_name 
                          FROM stock_logs sl 
                          LEFT JOIN products p ON sl.product_id = p. id 
                          LEFT JOIN users u ON sl.user_id = u.id 
                          WHERE sl.type = :type 
                          ORDER BY sl.created_at DESC');
        $this->db->bind(':type', $type);
        return $this->db->resultSet();
    }
}