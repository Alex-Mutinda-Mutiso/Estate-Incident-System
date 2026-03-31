<?php
require dirname(__DIR__) . '/bootstrap.php';
require dirname(__DIR__) . '/helpers/AccessHelper.php';

AccessHelper::requireRole('admin');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id    = $_POST['id'] ?? null;
    $action = $_POST['action'] ?? null;
    $role   = $_POST['role'] ?? null;

    if ($action === 'edit' && $id && $role) {
        $stmt = db()->prepare("UPDATE users SET role = ? WHERE id = ?");
        $success = $stmt->execute([$role, $id]);

        if ($success) {
            $_SESSION['flash_message'] = "User #{$id} role updated to {$role}.";
            header("Location: index.php?route=manage_users&success=role_updated");
            exit;
        } else {
            $_SESSION['flash_message'] = "Update failed for User #{$id}.";
            header("Location: index.php?route=manage_users&error=update_failed");
            exit;
        }
    } elseif ($action === 'delete' && $id) {
        $stmt = db()->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$id]);
        $_SESSION['flash_message'] = "User #{$id} deleted.";
        header("Location: index.php?route=manage_users&success=user_deleted");
        exit;
    } else {
        $_SESSION['flash_message'] = "Update failed — missing role or ID.";
        header("Location: index.php?route=manage_users&error=update_failed");
        exit;
    }
}