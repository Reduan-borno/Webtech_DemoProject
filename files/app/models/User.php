<?php
/**
 * User Model
 * Handles all user-related database operations
 */

class User {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    /**
     * Find user by ID
     */
    public function findById($id) {
        $this->db->query('SELECT * FROM users WHERE id = : id');
        $this->db->bind(': id', $id);
        return $this->db->single();
    }

    /**
     * Find user by email
     */
    public function findByEmail($email) {
        $this->db->query('SELECT * FROM users WHERE email = :email');
        $this->db->bind(': email', $email);
        return $this->db->single();
    }

    /**
     * Find user by username
     */
    public function findByUsername($username) {
        $this->db->query('SELECT * FROM users WHERE username = :username');
        $this->db->bind(':username', $username);
        return $this->db->single();
    }

    /**
     * Create new user
     */
    public function create($data) {
        $this->db->query('INSERT INTO users (username, email, password, full_name, phone, role, status) 
                          VALUES (:username, :email, :password, :full_name, :phone, : role, :status)');
        $this->db->bind(': username', $data['username']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':password', password_hash($data['password'], PASSWORD_DEFAULT));
        $this->db->bind(': full_name', $data['full_name']);
        $this->db->bind(': phone', $data['phone'] ??  null);
        $this->db->bind(': role', $data['role'] ?? 'customer');
        $this->db->bind(': status', $data['status'] ?? 'approved');
        
        if ($this->db->execute()) {
            return $this->db->lastInsertId();
        }
        return false;
    }

    /**
     * Update user
     */
    public function update($id, $data) {
        $sql = 'UPDATE users SET full_name = :full_name, phone = :phone, address = :address';
        
        if (isset($data['email'])) {
            $sql .= ', email = :email';
        }
        if (isset($data['profile_image'])) {
            $sql .= ', profile_image = : profile_image';
        }
        
        $sql .= ' WHERE id = :id';
        
        $this->db->query($sql);
        $this->db->bind(':full_name', $data['full_name']);
        $this->db->bind(':phone', $data['phone'] ?? null);
        $this->db->bind(':address', $data['address'] ?? null);
        $this->db->bind(':id', $id);
        
        if (isset($data['email'])) {
            $this->db->bind(':email', $data['email']);
        }
        if (isset($data['profile_image'])) {
            $this->db->bind(':profile_image', $data['profile_image']);
        }
        
        return $this->db->execute();
    }

    /**
     * Update password
     */
    public function updatePassword($id, $password) {
        $this->db->query('UPDATE users SET password = :password WHERE id = :id');
        $this->db->bind(':password', password_hash($password, PASSWORD_DEFAULT));
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    /**
     * Delete user
     */
    public function delete($id) {
        $this->db->query('DELETE FROM users WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    /**
     * Get all users
     */
    public function getAll($role = null) {
        if ($role) {
            $this->db->query('SELECT * FROM users WHERE role = :role ORDER BY created_at DESC');
            $this->db->bind(':role', $role);
        } else {
            $this->db->query('SELECT * FROM users ORDER BY created_at DESC');
        }
        return $this->db->resultSet();
    }

    /**
     * Get pending employees
     */
    public function getPendingEmployees() {
        $this->db->query('SELECT * FROM users WHERE role = : role AND status = : status ORDER BY created_at DESC');
        $this->db->bind(': role', 'employee');
        $this->db->bind(': status', 'pending');
        return $this->db->resultSet();
    }

    /**
     * Update user status
     */
    public function updateStatus($id, $status) {
        $this->db->query('UPDATE users SET status = :status WHERE id = :id');
        $this->db->bind(':status', $status);
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    /**
     * Verify password
     */
    public function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }

    /**
     * Set reset token
     */
    public function setResetToken($email, $token) {
        $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));
        $this->db->query('UPDATE users SET reset_token = : token, reset_token_expiry = : expiry WHERE email = :email');
        $this->db->bind(':token', $token);
        $this->db->bind(':expiry', $expiry);
        $this->db->bind(':email', $email);
        return $this->db->execute();
    }

    /**
     * Verify reset token
     */
    public function verifyResetToken($email, $token) {
        $this->db->query('SELECT * FROM users WHERE email = :email AND reset_token = :token AND reset_token_expiry > NOW()');
        $this->db->bind(':email', $email);
        $this->db->bind(': token', $token);
        return $this->db->single();
    }

    /**
     * Clear reset token
     */
    public function clearResetToken($email) {
        $this->db->query('UPDATE users SET reset_token = NULL, reset_token_expiry = NULL WHERE email = :email');
        $this->db->bind(':email', $email);
        return $this->db->execute();
    }

    /**
     * Count users by role
     */
    public function countByRole($role) {
        $this->db->query('SELECT COUNT(*) as count FROM users WHERE role = :role');
        $this->db->bind(':role', $role);
        $result = $this->db->single();
        return $result['count'];
    }
}