<? php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/User.php';

class AuthController {
    private $db;
    private $user;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->user = new User($this->db);
    }

    // Login
    public function login($username, $password) {
        $result = $this->user->login($username, $password);
        
        if (is_array($result) && isset($result['error'])) {
            return ['success' => false, 'message' => $result['error']];
        }
        
        if ($result) {
            session_start();
            $_SESSION['user_id'] = $result['id'];
            $_SESSION['username'] = $result['username'];
            $_SESSION['role'] = $result['role'];
            $_SESSION['full_name'] = $result['full_name'];
            
            return [
                'success' => true, 
                'role' => $result['role'],
                'message' => 'Login successful'
            ];
        }
        
        return ['success' => false, 'message' => 'Invalid credentials'];
    }

    // Register
    public function register($data) {
        $this->user->username = $data['username'];
        $this->user->email = $data['email'];
        $this->user->password = $data['password'];
        $this->user->full_name = $data['full_name'];
        $this->user->phone = $data['phone'] ?? '';
        $this->user->address = $data['address'] ?? '';
        $this->user->role = $data['role'];
        
        // Employees need approval, customers are auto-approved
        $this->user->status = ($data['role'] === 'employee') ? 'pending' : 'approved';

        if ($this->user->create()) {
            $message = ($data['role'] === 'employee') 
                ? 'Registration successful.  Please wait for admin approval.' 
                : 'Registration successful. You can now login.';
            return ['success' => true, 'message' => $message];
        }
        
        return ['success' => false, 'message' => 'Registration failed.  Username or email may already exist.'];
    }

    // Logout
    public function logout() {
        session_start();
        session_destroy();
        return ['success' => true, 'message' => 'Logged out successfully'];
    }

    // Change password
    public function changePassword($userId, $currentPassword, $newPassword) {
        $user = $this->user->getById($userId);
        
        if (!password_verify($currentPassword, $user['password'])) {
            return ['success' => false, 'message' => 'Current password is incorrect'];
        }
        
        if ($this->user->changePassword($userId, $newPassword)) {
            return ['success' => true, 'message' => 'Password changed successfully'];
        }
        
        return ['success' => false, 'message' => 'Failed to change password'];
    }

    // Check if logged in
    public function isLoggedIn() {
        session_start();
        return isset($_SESSION['user_id']);
    }

    // Get current user
    public function getCurrentUser() {
        session_start();
        if (isset($_SESSION['user_id'])) {
            return $this->user->getById($_SESSION['user_id']);
        }
        return null;
    }
}
?>