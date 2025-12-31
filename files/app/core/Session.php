<?php
/**
 * Session Class
 * Handles session management
 */

class Session {
    /**
     * Start session
     */
    public static function start() {
        if (session_status() === PHP_SESSION_NONE) {
            session_name(SESSION_NAME);
            session_start();
        }
    }

    /**
     * Set session value
     */
    public static function set($key, $value) {
        $_SESSION[$key] = $value;
    }

    /**
     * Get session value
     */
    public static function get($key) {
        return isset($_SESSION[$key]) ? $_SESSION[$key] : null;
    }

    /**
     * Check if session key exists
     */
    public static function has($key) {
        return isset($_SESSION[$key]);
    }

    /**
     * Remove session value
     */
    public static function remove($key) {
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }
    }

    /**
     * Destroy session
     */
    public static function destroy() {
        session_unset();
        session_destroy();
    }

    /**
     * Set flash message
     */
    public static function setFlash($type, $message) {
        $_SESSION['flash'] = [
            'type' => $type,
            'message' => $message
        ];
    }

    /**
     * Get and clear flash message
     */
    public static function getFlash() {
        if (isset($_SESSION['flash'])) {
            $flash = $_SESSION['flash'];
            unset($_SESSION['flash']);
            return $flash;
        }
        return null;
    }

    /**
     * Check if user is logged in
     */
    public static function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }

    /**
     * Get current user ID
     */
    public static function getUserId() {
        return isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
    }

    /**
     * Get current user role
     */
    public static function getUserRole() {
        return isset($_SESSION['user_role']) ? $_SESSION['user_role'] : null;
    }

    /**
     * Check if user has specific role
     */
    public static function hasRole($role) {
        return self::getUserRole() === $role;
    }
}