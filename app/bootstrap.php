<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$timeout_duration = 700; 

if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $timeout_duration) {
    session_unset();
    session_destroy();
    setcookie("remember_token", "", time() - 3600, "/"); 
    header("Location: index.php?route=login&error=session_expired");
    exit;
}
$_SESSION['last_activity'] = time();

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/helpers/AccessHelper.php';

$conn = db();

if (!isset($_SESSION['user_id']) && !isset($_SESSION['admin_id']) && isset($_COOKIE['remember_token'])) {
    $token = $_COOKIE['remember_token'];

    $stmt = $conn->prepare("SELECT id, name, role, remember_token, email FROM users WHERE remember_token IS NOT NULL");
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($users as $user) {
        if (password_verify($token, $user['remember_token'])) {
            $_SESSION['user_id']  = $user['id'];
            $_SESSION['username'] = $user['name'];
            $_SESSION['role']     = strtolower($user['role']);
            $_SESSION['email']    = $user['email'];
            break;
        }
    }

    if (!isset($_SESSION['user_id']) && !isset($_SESSION['admin_id'])) {
        $stmt = $conn->prepare("SELECT id, username, remember_token, email FROM admins WHERE remember_token IS NOT NULL");
        $stmt->execute();
        $admins = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($admins as $admin) {
            if (password_verify($token, $admin['remember_token'])) {
                $_SESSION['admin_id']  = $admin['id'];
                $_SESSION['username']  = $admin['username'];
                $_SESSION['role']      = 'admin';
                $_SESSION['email']     = $admin['email'];
                break;
            }
        }
    }
}

$stmt = $conn->prepare("SELECT value FROM settings WHERE name = 'maintenance_mode'");
$stmt->execute();
$mode = $stmt->fetchColumn();

if ($mode === 'on' && (!isset($_SESSION['role']) || $_SESSION['role'] !== 'dev')) {
    $stmt = $conn->prepare("SELECT value FROM settings WHERE name = 'maintenance_message'");
    $stmt->execute();
    $message = $stmt->fetchColumn() ?: "We’re currently performing updates to improve the system.";

    $stmt = $conn->prepare("SELECT value FROM settings WHERE name = 'maintenance_email'");
    $stmt->execute();
    $email = $stmt->fetchColumn() ?: "support@example.com";

    echo "<!DOCTYPE html>
    <html>
    <head><title>Maintenance</title></head>
    <body style='font-family:Arial; text-align:center; margin-top:50px;'>
        <h1>🚧 Site Under Maintenance 🚧</h1>
        <p>{$message}</p>
        <p>If urgent, contact: <a href='mailto:{$email}'>{$email}</a></p>
    </body>
    </html>";
    exit;
}