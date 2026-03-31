<?php
require __DIR__ . '/../app/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usernameOrEmail = $_POST['username'] ?? null;
    $password = $_POST['password'] ?? null;
    $remember = isset($_POST['remember']);  


    if (!$usernameOrEmail || !$password) {
        $_SESSION['flash_message'] = "Missing login fields.";
        include __DIR__ . '/views/auth/login.php';
        exit;
    }

    try {
        $conn = db();  

        $stmt = $conn->prepare("SELECT COUNT(*) FROM login_attempts 
                                WHERE username_or_email=? 
                                AND success=0 
                                AND attempt_time > (NOW() - INTERVAL 10 MINUTE)");
        $stmt->execute([$usernameOrEmail]);
        $failedAttempts = $stmt->fetchColumn();

        if ($failedAttempts >= 5) {
            $block = $conn->prepare("INSERT INTO blocked_attempts (username_or_email, ip_address, attempts) VALUES (?, ?, ?)");
            $block->execute([$usernameOrEmail, $_SERVER['REMOTE_ADDR'], $failedAttempts]);

            $_SESSION['flash_message'] = "Too many failed attempts. Try again later.";
            include __DIR__ . '/views/auth/login.php';
            exit;
        }

        $stmt = $conn->prepare("SELECT * FROM admins WHERE email = ? OR username = ?");
        $stmt->execute([$usernameOrEmail, $usernameOrEmail]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($admin && password_verify($password, $admin['password_hash'])) {
            session_regenerate_id(true);  

            $_SESSION['user_id']  = $admin['id'];
            $_SESSION['username'] = $admin['username'];
            $_SESSION['role']     = 'admin';
            $_SESSION['email']    = $admin['email'];

            if ($remember) {
                $token = bin2hex(random_bytes(32));
                $hashedToken = password_hash($token, PASSWORD_DEFAULT);
                setcookie("remember_token", $token, time() + (86400 * 30), "/", "", false, true);

                $update = $conn->prepare("UPDATE admins SET remember_token=? WHERE id=?");
                $update->execute([$hashedToken, $admin['id']]);
            }

            $log = $conn->prepare("INSERT INTO login_attempts (username_or_email, ip_address, success) VALUES (?, ?, 1)");
            $log->execute([$usernameOrEmail, $_SERVER['REMOTE_ADDR']]);

            header("Location: index.php?route=admin_dashboard");
            exit;
        }

        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? OR name = ?");
        $stmt->execute([$usernameOrEmail, $usernameOrEmail]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password_hash'])) {
            session_regenerate_id(true);

            $_SESSION['user_id']  = $user['id'];
            $_SESSION['username'] = $user['name'];
            $_SESSION['role']     = $user['role'];   
            $_SESSION['email']    = $user['email'];

            if ($remember) {
                $token = bin2hex(random_bytes(32));
                $hashedToken = password_hash($token, PASSWORD_DEFAULT);
                setcookie("remember_token", $token, time() + (86400 * 30), "/", "", false, true);

                $update = $conn->prepare("UPDATE users SET remember_token=? WHERE id=?");
                $update->execute([$hashedToken, $user['id']]);
            }

            $log = $conn->prepare("INSERT INTO login_attempts (username_or_email, ip_address, success) VALUES (?, ?, 1)");
            $log->execute([$usernameOrEmail, $_SERVER['REMOTE_ADDR']]);

            switch ($user['role']) {
                case 'dev':
                    header("Location: index.php?route=dev_dashboard");
                    break;
                case 'resident':
                    header("Location: index.php?route=report");
                    break;
                case 'staff':
                    header("Location: index.php?route=staff_dashboard");
                    break;
                case 'admin':
                    header("Location: index.php?route=admin_dashboard");
                    break;
                default:
                    header("Location: index.php?route=home");
            }
            exit;
        }

        $log = $conn->prepare("INSERT INTO login_attempts (username_or_email, ip_address, success) VALUES (?, ?, 0)");
        $log->execute([$usernameOrEmail, $_SERVER['REMOTE_ADDR']]);

        $remaining = 5 - ($failedAttempts + 1);
        $_SESSION['flash_message'] = $remaining > 0
            ? "Incorrect email or password. You have {$remaining} attempt(s) left."
            : "Incorrect email or password. You are now blocked for 10 minutes.";

        include __DIR__ . '/views/auth/login.php';
        exit;

    } catch (PDOException $e) {
        $_SESSION['flash_message'] = "Database Error: " . $e->getMessage();
        include __DIR__ . '/views/auth/login.php';
        exit;
    }
}
?>