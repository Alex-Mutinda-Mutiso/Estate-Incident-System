<?php
require dirname(__DIR__, 3) . '/app/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['token'])) {
    $token           = $_GET['token'];
    $newPassword     = $_POST['new_password'] ?? null;
    $confirmPassword = $_POST['confirm_password'] ?? null;

    if ($newPassword && $confirmPassword) {
        if ($newPassword !== $confirmPassword) {
            $_SESSION['flash_message'] = "Passwords do not match.";
            header("Location: index.php?route=reset_password&token=" . urlencode($token));
            exit;
        }

        $errors = [];
        if (strlen($newPassword) < 8) $errors[] = "Password must be at least 8 characters long.";
        if (!preg_match('/[A-Z]/', $newPassword)) $errors[] = "Password must contain at least one uppercase letter.";
        if (!preg_match('/[a-z]/', $newPassword)) $errors[] = "Password must contain at least one lowercase letter.";
        if (!preg_match('/[0-9]/', $newPassword)) $errors[] = "Password must contain at least one number.";
        if (!preg_match('/[\W]/', $newPassword)) $errors[] = "Password must contain at least one special character.";

        if (!empty($errors)) {
            $_SESSION['flash_message'] = implode(" ", $errors);
            header("Location: index.php?route=reset_password&token=" . urlencode($token));
            exit;
        }

        $stmt = db()->prepare("SELECT user_id, expires_at FROM password_resets WHERE token = ?");
        $stmt->execute([$token]);
        $reset = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($reset && strtotime($reset['expires_at']) > time()) {
            $hashed = password_hash($newPassword, PASSWORD_DEFAULT);

            $stmt = db()->prepare("UPDATE users SET password = ? WHERE id = ?");
            $stmt->execute([$hashed, $reset['user_id']]);

            $stmt = db()->prepare("DELETE FROM password_resets WHERE token = ?");
            $stmt->execute([$token]);

            $_SESSION['flash_message'] = "Password updated successfully. You can now log in.";
            header("Location: index.php?route=login&success=password_reset");
            exit;
        } else {
            $_SESSION['flash_message'] = "Invalid or expired reset link.";
            header("Location: index.php?route=forgot_password&error=invalid_token");
            exit;
        }
    } else {
        $_SESSION['flash_message'] = "Please enter and confirm your new password.";
        header("Location: index.php?route=reset_password&token=" . urlencode($token));
        exit;
    }
}