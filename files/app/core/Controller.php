<?php
/**
 * Base Controller Class
 * All controllers extend this class
 */

class Controller {
    protected $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    /**
     * Load a model
     */
    protected function model($model) {
        require_once APP_ROOT . '/app/models/' . $model . '.php';
        return new $model();
    }

    /**
     * Load a view with data
     */
    protected function view($view, $data = []) {
        // Extract data to make variables available in view
        extract($data);
        
        // Check if view file exists
        $viewFile = APP_ROOT .  '/app/views/' . $view . '.php';
        if (file_exists($viewFile)) {
            require_once $viewFile;
        } else {
            die('View not found: ' . $view);
        }
    }

    /**
     * Redirect to URL
     */
    protected function redirect($url) {
        header('Location: ' .  APP_URL . '/' . $url);
        exit;
    }

    /**
     * Check if user is logged in
     */
    protected function requireLogin() {
        if (!Session::isLoggedIn()) {
            Session::setFlash('error', 'Please login to access this page');
            $this->redirect('login');
        }
    }

    /**
     * Check if user has specific role
     */
    protected function requireRole($roles) {
        $this->requireLogin();
        
        if (! is_array($roles)) {
            $roles = [$roles];
        }
        
        if (!in_array(Session:: getUserRole(), $roles)) {
            Session::setFlash('error', 'You do not have permission to access this page');
            $this->redirect('dashboard');
        }
    }

    /**
     * Return JSON response
     */
    protected function json($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    /**
     * Sanitize input
     */
    protected function sanitize($data) {
        if (is_array($data)) {
            return array_map([$this, 'sanitize'], $data);
        }
        return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
    }

    /**
     * Validate CSRF token
     */
    protected function validateCsrf() {
        if (! isset($_POST['csrf_token']) || $_POST['csrf_token'] !== Session::get('csrf_token')) {
            Session:: setFlash('error', 'Invalid request');
            $this->redirect('dashboard');
        }
    }

    /**
     * Generate CSRF token
     */
    protected function generateCsrf() {
        $token = bin2hex(random_bytes(32));
        Session::set('csrf_token', $token);
        return $token;
    }
}