<?php
class AuthHelper {
    public static function check() {
        return !empty($_SESSION['user_id']);
    }
    public static function role() {
        return $_SESSION['role'] ?? null;
    }
}
