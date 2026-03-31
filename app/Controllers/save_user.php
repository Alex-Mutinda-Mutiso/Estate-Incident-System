<?php
require dirname(__DIR__) . '/bootstrap.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? null;
    $email = $_POST['email'] ?? null;
    $password = $_POST['password'] ?? null;
    $role = $_POST['role'] ?? 'resident';

    if ($name && $email && $password) {
        $hash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = db()->prepare("INSERT INTO users (name, email, password_hash, role) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $email, $hash, $role]);

        $_SESSION['flash_message'] = "User {$name} added successfully.";
    } else {
        $_SESSION['flash_message'] = "Missing required fields.";
    }

    header("Location: index.php?route=manage_users");
    exit;
}