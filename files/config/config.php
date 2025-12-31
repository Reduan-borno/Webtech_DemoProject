<? php
/**
 * Configuration File
 * Contains all application settings
 */

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'webtech_demo');
define('DB_USER', 'root');
define('DB_PASS', ''); // Default XAMPP password is empty

// Application Configuration
define('APP_NAME', 'Webtech Demo Project');
define('APP_URL', 'http://localhost/Webtech_DemoProject');
define('APP_ROOT', dirname(dirname(__FILE__)));

// Session Configuration
define('SESSION_NAME', 'webtech_session');
define('SESSION_LIFETIME', 3600); // 1 hour

// File Upload Configuration
define('UPLOAD_PATH', APP_ROOT . '/public/uploads/');
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif']);

// Pagination
define('ITEMS_PER_PAGE', 10);

// Error Reporting (set to 0 in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Timezone
date_default_timezone_set('Asia/Dhaka');