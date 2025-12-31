<?php
/**
 * Product Model
 * Handles all product-related database operations
 */

class Product {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    /**
     * Find product by ID
     */
    public function findById($id) {
        $this->db->query('SELECT p.*, c.name as category_name 
                          FROM products p 
                          LEFT JOIN categories c ON p.category_id = c.id 
                          WHERE p.id = :id');
        $this->db->bind(': id', $id);
        return $this->db->single();
    }

    /**
     * Get all products
     */
    public function getAll($status = null) {
        $sql = 'SELECT p.*, c. name as category_name 
                FROM products p 
                LEFT JOIN categories c ON p. category_id = c.id';
        
        if ($status) {
            $sql .= ' WHERE p.status = :status';
        }
        
        $sql .= ' ORDER BY p.created_at DESC';
        
        $this->db->query($sql);
        
        if ($status) {
            $this->db->bind(':status', $status);
        }
        
        return $this->db->resultSet();
    }

    /**
     * Get products by category
     */
    public function getByCategory($categoryId) {
        $this->db->query('SELECT p.*, c.name as category_name 
                          FROM products p 
                          LEFT JOIN categories c ON p.category_id = c. id 
                          WHERE p.category_id = :category_id AND p.status = : status 
                          ORDER BY p.created_at DESC');
        $this->db->bind(':category_id', $categoryId);
        $this->db->bind(':status', 'active');
        return $this->db->resultSet();
    }

    /**
     * Search products
     */
    public function search($keyword, $categoryId = null) {
        $sql = 'SELECT p.*, c.name as category_name 
                FROM products p 
                LEFT JOIN categories c ON p. category_id = c.id 
                WHERE p.status = :status AND (p.name LIKE :keyword OR p. description LIKE :keyword OR p.brand LIKE :keyword)';
        
        if ($categoryId) {
            $sql . = ' AND p. category_id = : category_id';
        }
        
        $sql .= ' ORDER BY p.created_at DESC';
        
        $this->db->query($sql);
        $this->db->bind(':status', 'active');
        $this->db->bind(':keyword', '%' . $keyword .  '%');
        
        if ($categoryId) {
            $this->db->bind(':category_id', $categoryId);
        }
        
        return $this->db->resultSet();
    }

    /**
     * Create product
     */
    public function create($data) {
        $this->db->query('INSERT INTO products (name, description, price, category_id, image, stock_quantity, brand, specifications, created_by) 
                          VALUES (:name, :description, :price, :category_id, :image, : stock_quantity, : brand, :specifications, :created_by)');
        $this->db->bind(': name', $data['name']);
        $this->db->bind(':description', $data['description'] ?? null);
        $this->db->bind(': price', $data['price']);
        $this->db->bind(':category_id', $data['category_id']);
        $this->db->bind(':image', $data['image'] ?? 'default-product.png');
        $this->db->bind(': stock_quantity', $data['stock_quantity'] ?? 0);
        $this->db->bind(': brand', $data['brand'] ?? null);
        $this->db->bind(':specifications', $data['specifications'] ?? null);
        $this->db->bind(':created_by', $data['created_by']);
        
        if ($this->db->execute()) {
            return $this->db->lastInsertId();
        }
        return false;
    }

    /**
     * Update product
     */
    public function update($id, $data) {
        $sql = 'UPDATE products SET name = :name, description = :description, category_id = :category_id, 
                brand = :brand, specifications = :specifications';
        
        if (isset($data['image'])) {
            $sql .= ', image = :image';
        }
        if (isset($data['status'])) {
            $sql .= ', status = :status';
        }
        
        $sql .= ' WHERE id = :id';
        
        $this->db->query($sql);
        $this->db->bind(':name', $data['name']);
        $this->db->bind(': description', $data['description'] ?? null);
        $this->db->bind(':category_id', $data['category_id']);
        $this->db->bind(':brand', $data['brand'] ?? null);
        $this->db->bind(':specifications', $data['specifications'] ?? null);
        $this->db->bind(':id', $id);
        
        if (isset($data['image'])) {
            $this->db->bind(':image', $data['image']);
        }
        if (isset($data['status'])) {
            $this->db->bind(':status', $data['status']);
        }
        
        return $this->db->execute();
    }

    /**
     * Update price
     */
    public function updatePrice($id, $price, $offerPrice = null) {
        $this->db->query('UPDATE products SET price = :price, offer_price = : offer_price WHERE id = :id');
        $this->db->bind(':price', $price);
        $this->db->bind(':offer_price', $offerPrice);
        $this->db->bind(': id', $id);
        return $this->db->execute();
    }

    /**
     * Update stock
     */
    public function updateStock($id, $quantity) {
        $this->db->query('UPDATE products SET stock_quantity = :quantity WHERE id = :id');
        $this->db->bind(': quantity', $quantity);
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    /**
     * Delete product
     */
    public function delete($id) {
        $this->db->query('DELETE FROM products WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    /**
     * Count products
     */
    public function count() {
        $this->db->query('SELECT COUNT(*) as count FROM products');
        $result = $this->db->single();
        return $result['count'];
    }

    /**
     * Get low stock products
     */
    public function getLowStock($threshold = 10) {
        $this->db->query('SELECT p.*, c.name as category_name 
                          FROM products p 
                          LEFT JOIN categories c ON p.category_id = c. id 
                          WHERE p.stock_quantity <= :threshold 
                          ORDER BY p.stock_quantity ASC');
        $this->db->bind(':threshold', $threshold);
        return $this->db->resultSet();
    }
}