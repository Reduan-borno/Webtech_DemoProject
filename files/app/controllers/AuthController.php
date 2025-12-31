<? php
/**
 * AuthController
 * Handles authentication:  login, register, logout, password reset
 */

class AuthController extends Controller {
    private $userModel;

    public function __construct() {
        parent::__construct();
        $this->userModel = $this->model('User');
    }

    /**
     * Display login page
     */
    public function login() {
        // Redirect if already logged in
        if (Session::isLoggedIn()) {
            $this->redirect('dashboard');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $this->sanitize($_POST['email'] ?? '');
            $password = $_POST['password'] ??  '';

            // Validate input
            $errors = [];
            if (empty($email)) {
                $errors[] = 'Email is required';
            }
            if (empty($password)) {
                $errors[] = 'Password is required';
            }

            if (empty($errors)) {
                $user = $this->userModel->findByEmail($email);
                
                if ($user && $this->userModel->verifyPassword($password, $user['password'])) {
                    // Check if user is approved (for employees)
                    if ($user['role'] === 'employee' && $user['status'] !== 'approved') {
                        Session::setFlash('error', 'Your account is pending approval');
                    } else {
                        // Set session data
                        Session:: set('user_id', $user['id']);
                        Session:: set('user_role', $user['role']);
                        Session::set('user_name', $user['full_name']);
                        Session::set('user_email', $user['email']);
                        
                        Session::setFlash('success', 'Welcome back, ' . $user['full_name'] . '!');
                        $this->redirect('dashboard');
                    }
                } else {
                    Session::setFlash('error', 'Invalid email or password');
                }
            } else {
                Session::setFlash('error', implode('<br>', $errors));
            }
        }

        $this->view('auth/login', [
            'title' => 'Login',
            'csrf_token' => $this->generateCsrf()
        ]);
    }

    /**
     * Display register page
     */
    public function register() {
        if (Session::isLoggedIn()) {
            $this->redirect('dashboard');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'username' => $this->sanitize($_POST['username'] ?? ''),
                'email' => $this->sanitize($_POST['email'] ?? ''),
                'password' => $_POST['password'] ??  '',
                'confirm_password' => $_POST['confirm_password'] ?? '',
                'full_name' => $this->sanitize($_POST['full_name'] ?? ''),
                'phone' => $this->sanitize($_POST['phone'] ?? ''),
                'role' => $this->sanitize($_POST['role'] ??  'customer')
            ];

            // Validate
            $errors = [];
            if (empty($data['username'])) {
                $errors[] = 'Username is required';
            } elseif ($this->userModel->findByUsername($data['username'])) {
                $errors[] = 'Username already exists';
            }
            
            if (empty($data['email'])) {
                $errors[] = 'Email is required';
            } elseif (! filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Invalid email format';
            } elseif ($this->userModel->findByEmail($data['email'])) {
                $errors[] = 'Email already exists';
            }
            
            if (empty($data['password'])) {
                $errors[] = 'Password is required';
            } elseif (strlen($data['password']) < 6) {
                $errors[] = 'Password must be at least 6 characters';
            }
            
            if ($data['password'] !== $data['confirm_password']) {
                $errors[] = 'Passwords do not match';
            }
            
            if (empty($data['full_name'])) {
                $errors[] = 'Full name is required';
            }

            // Only allow customer or employee registration
            if (! in_array($data['role'], ['customer', 'employee'])) {
                $data['role'] = 'customer';
            }

            // Set status based on role
            $data['status'] = ($data['role'] === 'employee') ? 'pending' : 'approved';

            if (empty($errors)) {
                $userId = $this->userModel->create($data);
                
                if ($userId) {
                    if ($data['role'] === 'employee') {
                        Session::setFlash('success', 'Registration successful!  Please wait for admin approval.');
                    } else {
                        Session::setFlash('success', 'Registration successful! Please login.');
                    }
                    $this->redirect('login');
                } else {
                    Session::setFlash('error', 'Registration failed. Please try again.');
                }
            } else {
                Session::setFlash('error', implode('<br>', $errors));
            }
        }

        $this->view('auth/register', [
            'title' => 'Register',
            'csrf_token' => $this->generateCsrf()
        ]);
    }

    /**
     * Logout user
     */
    public function logout() {
        Session::destroy();
        session_start();
        Session::setFlash('success', 'You have been logged out');
        $this->redirect('login');
    }

    /**
     * Reset password
     */
    public function resetPassword() {
        if (Session::isLoggedIn()) {
            $this->redirect('dashboard');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $this->sanitize($_POST['email'] ?? '');
            $token = $this->sanitize($_POST['token'] ??  '');
            $newPassword = $_POST['new_password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';

            if (! empty($token)) {
                // Verify token and reset password
                $user = $this->userModel->verifyResetToken($email, $token);
                
                if ($user) {
                    if (empty($newPassword) || strlen($newPassword) < 6) {
                        Session::setFlash('error', 'Password must be at least 6 characters');
                    } elseif ($newPassword !== $confirmPassword) {
                        Session::setFlash('error', 'Passwords do not match');
                    } else {
                        $this->userModel->updatePassword($user['id'], $newPassword);
                        $this->userModel->clearResetToken($email);
                        Session:: setFlash('success', 'Password reset successful!  Please login.');
                        $this->redirect('login');
                    }
                } else {
                    Session::setFlash('error', 'Invalid or expired reset token');
                }
            } else {
                // Generate reset token
                $user = $this->userModel->findByEmail($email);
                
                if ($user) {
                    $token = bin2hex(random_bytes(32));
                    $this->userModel->setResetToken($email, $token);
                    
                    // In a real application, send email with reset link
                    // For demo purposes, we'll show the token
                    Session::setFlash('success', 'Reset token generated. Token: ' . $token .  ' (In production, this would be sent via email)');
                } else {
                    Session::setFlash('error', 'Email not found');
                }
            }
        }

        $this->view('auth/reset-password', [
            'title' => 'Reset Password',
            'csrf_token' => $this->generateCsrf()
        ]);
    }
}