<?php
require dirname(__DIR__, 3) . '/app/bootstrap.php';
require dirname(__DIR__, 3) . '/app/security.php';
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php?route=login");
    exit;
}

error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $csrfToken = $_POST['csrf_token'] ?? '';

    if (!verify_csrf_token($csrfToken)) {
        $_SESSION['flash_message'] = "❌ Invalid request (CSRF failed).";
        header("Location: index.php?route=admin_dashboard");
        exit;
    }

    if ($id) {
        try {
            $conn = db();

            // 🔧 Delete feedback linked to this complaint first
            $stmt = $conn->prepare("DELETE FROM feedback WHERE complaint_id = ?");
            $stmt->execute([$id]);

            // ✅ Now delete the complaint
            $stmt = $conn->prepare("DELETE FROM complaints WHERE id = ?");
            $stmt->execute([$id]);

            $_SESSION['flash_message'] = "✅ Case deleted successfully!";
        } catch (PDOException $e) {
            error_log("Delete case error: " . $e->getMessage());
            $_SESSION['flash_message'] = "❌ Error deleting case: " . $e->getMessage();
        }
    } else {
        $_SESSION['flash_message'] = "❌ Invalid case ID.";
    }

    header("Location: index.php?route=admin_dashboard");
    exit;
}