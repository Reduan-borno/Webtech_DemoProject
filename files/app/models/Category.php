<?php
/**
 * Category Model
 * Handles all category-related database operations
 */

class Category {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    /**
     * Find category by ID
     */
    public function findById($id) {
        $this->db->query('SELECT * FROM categories WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    /**
     * Get all categories
     */
    public function getAll($status = null) {
        if ($status) {
            $this->db->query('SELECT * FROM categories WHERE status = :status ORDER BY name ASC');
            $this->db->bind(':status', $status);
        } else {
            $this->db->query('SELECT * FROM categories ORDER BY name ASC');
        }
        return $this->db->resultSet();
    }

    /**
     * Create category
     */
    public function create($data) {
        $this->db->query('INSERT INTO categories (name, description) VALUES (:name, :description)');
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':description', $data['description'] ?? null);
        
        if ($this->db->execute()) {
            return $this->db->lastInsertId();
        }
        return false;
    }

    /**
     * Update category
     */
    public function update($id, $data) {
        $this->db->query('UPDATE categories SET name = :name, description = :description, status = :status WHERE id = :id');
        $this->db->bind(':name', $data['name']);
        $this->db->bind(': description', $data['description'] ?? null);
        $this->db->bind(':status', $data['status'] ?? 'active');
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    /**
     * Delete category
     */
    public function delete($id) {
        $this->db->query('DELETE FROM categories WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    /**
     * Count categories
     */
    public function count() {
        $this->db->query('SELECT COUNT(*) as count FROM categories');
        $result = $this->db->single();
        return $result['count'];
    }

    /**
     * Get category with product count
     */
    public function getWithProductCount() {
        $this->db->query('SELECT c.*, COUNT(p.id) as product_count 
                          FROM categories c 
                          LEFT JOIN products p ON c.id = p.category_id 
                          GROUP BY c.id 
                          ORDER BY c.name ASC');
        return $this->db->resultSet();
    }
}