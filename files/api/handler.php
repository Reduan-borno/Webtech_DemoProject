<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin:  *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

session_start();

require_once __DIR__ . '/../controllers/AuthController.php';
require_once __DIR__ . '/../controllers/AdminController.php';
require_once __DIR__ . '/../controllers/EmployeeController.php';
require_once __DIR__ . '/../controllers/CustomerController.php';

$action = $_REQUEST['action'] ?? '';
$response = ['success' => false, 'message' => 'Invalid action'];

// Auth Controller
$authController = new AuthController();

// Check authentication for protected routes
function requireAuth($roles = []) {
    if (! isset($_SESSION['user_id'])) {
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        exit;
    }
    if (! empty($roles) && !in_array($_SESSION['role'], $roles)) {
        echo json_encode(['success' => false, 'message' => 'Access denied']);
        exit;
    }
}

switch ($action) {
    // ============ AUTH