<?php
if (!class_exists('AccessHelper')) {
    class AccessHelper {
        public static function requireRole($role) {
            if (!isset($_SESSION['role']) || strtolower($_SESSION['role']) !== strtolower($role)) {
                header("Location: index.php?route=login");
                exit;
            }
        }

        public static function requireLogin() {
            if (!isset($_SESSION['user_id'])) {
                header("Location: index.php?route=login");
                exit;
            }
        }
    }
}