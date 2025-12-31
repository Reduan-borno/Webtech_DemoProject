<? php
/**
 * Wishlist Model
 * Handles wishlist operations
 */

class Wishlist {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    /**
     * Get user's wishlist
     */
    public function getByUser($userId) {
        $this->db->query('SELECT w.*, p.name, p.price, p.offer_price, p. image, c.name as category_name 
                          FROM wishlist w 
                          LEFT JOIN products p ON w.product_id = p.id 
                          LEFT JOIN categories c ON p.category_id = c. id 
                          WHERE w.user_id = :user_id 
                          ORDER BY w.created_at DESC');
        $this->db->bind(':user_id', $userId);
        return $this->db->resultSet();
    }

    /**
     * Add to wishlist
     */
    public function add($userId, $productId) {
        // Check if already exists
        if ($this->exists($userId, $productId)) {
            return true;
        }
        
        $this->db->query('INSERT INTO wishlist (user_id, product_id) VALUES (:user_id, :product_id)');
        $this->db->bind(':user_id', $userId);
        $this->db->bind(':product_id', $productId);
        return $this->db->execute();
    }

    /**
     * Remove from wishlist
     */
    public function remove($userId, $productId) {
        $this->db->query('DELETE FROM wishlist WHERE user_id = :user_id AND product_id = :product_id');
        $this->db->bind(':user_id', $userId);
        $this->db->bind(':product_id', $productId);
        return $this->db->execute();
    }

    /**
     * Check if product is in wishlist
     */
    public function exists($userId, $productId) {
        $this->db->query('SELECT id FROM wishlist WHERE user_id = :user_id AND product_id = :product_id');
        $this->db->bind(':user_id', $userId);
        $this->db->bind(':product_id', $productId);
        $this->db->execute();
        return $this->db->rowCount() > 0;
    }

    /**
     * Count wishlist items
     */
    public function countByUser($userId) {
        $this->db->query('SELECT COUNT(*) as count FROM wishlist WHERE user_id = :user_id');
        $this->db->bind(':user_id', $userId);
        $result = $this->db->single();
        return $result['count'];
    }

    /**
     * Clear user's wishlist
     */
    public function clearByUser($userId) {
        $this->db->query('DELETE FROM wishlist WHERE user_id = :user_id');
        $this->db->bind(':user_id', $userId);
        return $this->db->execute();
    }
}