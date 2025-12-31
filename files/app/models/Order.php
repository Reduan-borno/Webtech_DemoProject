<?php
/**
 * Order Model
 * Handles all order-related database operations
 */

class Order {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    /**
     * Find order by ID
     */
    public function findById($id) {
        $this->db->query('SELECT o.*, u.full_name as customer_name, u.email as customer_email 
                          FROM orders o 
                          LEFT JOIN users u ON o.customer_id = u. id 
                          WHERE o.id = :id');
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    /**
     * Get orders by customer
     */
    public function getByCustomer($customerId) {
        $this->db->query('SELECT * FROM orders WHERE customer_id = : customer_id ORDER BY created_at DESC');
        $this->db->bind(':customer_id', $customerId);
        return $this->db->resultSet();
    }

    /**
     * Get order items
     */
    public function getOrderItems($orderId) {
        $this->db->query('SELECT oi.*, p. name as product_name, p.image 
                          FROM order_items oi 
                          LEFT JOIN products p ON oi.product_id = p.id 
                          WHERE oi.order_id = :order_id');
        $this->db->bind(':order_id', $orderId);
        return $this->db->resultSet();
    }

    /**
     * Create order
     */
    public function create($data) {
        $orderNumber = 'ORD-' .  date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
        
        $this->db->query('INSERT INTO orders (order_number, customer_id, total_amount, shipping_address, payment_method, notes) 
                          VALUES (:order_number, :customer_id, :total_amount, :shipping_address, :payment_method, :notes)');
        $this->db->bind(': order_number', $orderNumber);
        $this->db->bind(':customer_id', $data['customer_id']);
        $this->db->bind(':total_amount', $data['total_amount']);
        $this->db->bind(':shipping_address', $data['shipping_address']);
        $this->db->bind(':payment_method', $data['payment_method'] ?? 'cash_on_delivery');
        $this->db->bind(':notes', $data['notes'] ?? null);
        
        if ($this->db->execute()) {
            return $this->db->lastInsertId();
        }
        return false;
    }

    /**
     * Add order item
     */
    public function addOrderItem($orderId, $productId, $quantity, $price) {
        $this->db->query('INSERT INTO order_items (order_id, product_id, quantity, price) 
                          VALUES (:order_id, :product_id, :quantity, : price)');
        $this->db->bind(':order_id', $orderId);
        $this->db->bind(':product_id', $productId);
        $this->db->bind(':quantity', $quantity);
        $this->db->bind(': price', $price);
        return $this->db->execute();
    }

    /**
     * Update order status
     */
    public function updateStatus($id, $status) {
        $this->db->query('UPDATE orders SET status = : status WHERE id = : id');
        $this->db->bind(':status', $status);
        $this->db->bind(': id', $id);
        return $this->db->execute();
    }

    /**
     * Count orders by customer
     */
    public function countByCustomer($customerId) {
        $this->db->query('SELECT COUNT(*) as count FROM orders WHERE customer_id = :customer_id');
        $this->db->bind(': customer_id', $customerId);
        $result = $this->db->single();
        return $result['count'];
    }

    /**
     * Get total spent by customer
     */
    public function getTotalSpent($customerId) {
        $this->db->query('SELECT SUM(total_amount) as total FROM orders WHERE customer_id = : customer_id AND status != : status');
        $this->db->bind(':customer_id', $customerId);
        $this->db->bind(':status', 'cancelled');
        $result = $this->db->single();
        return $result['total'] ?? 0;
    }
}