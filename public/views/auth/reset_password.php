<?php
require dirname(__DIR__, 3) . '/app/bootstrap.php';
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['token'])) {
    $token = $_GET['token'];
    ?>
    <!DOCTYPE html>
    <html>
    <head>
      <title>Reset Password</title>
      <link rel="stylesheet" href="/estate_incident_system/public/css/styles.css">
      <style>
        body { font-family: Arial, sans-serif; background:#f4f6f9; }
        .login-container { display:flex; justify-content:center; align-items:center; height:100vh; }
        .login-box { background:#fff; padding:30px; border-radius:8px; box-shadow:0 0 10px rgba(0,0,0,0.1); width:300px; text-align:center; }
        .login-box h2 { margin-bottom:20px; }
        .login-box input { width:100%; padding:10px; margin:10px 0; border:1px solid #ddd; border-radius:4px; }
        .login-box button { width:100%; padding:10px; background:#2196f3; color:#fff; border:none; border-radius:4px; cursor:pointer; }
        .login-box button:hover { background:#1976d2; }
        .flash { background:#dff0d8; color:#3c763d; padding:10px; margin-bottom:15px; border:1px solid #d6e9c6; border-radius:6px; }
      </style>
    </head>
    <body>
      <div class="login-container">
        <div class="login-box">
          <div class="icon">🔒</div>
          <h2>Reset Password</h2>

          <?php if (!empty($_SESSION['flash_message'])): ?>
            <div class="flash"><?= htmlspecialchars($_SESSION['flash_message']) ?></div>
            <?php unset($_SESSION['flash_message']); ?>
          <?php endif; ?>

          <form method="post" action="index.php?route=reset_password_action&token=<?= htmlspecialchars($token) ?>">            <input type="password" name="new_password" placeholder="Enter new password" required>
            <input type="password" name="confirm_password" placeholder="Confirm new password" required>
            <button type="submit">Update Password</button>
          </form>
        </div>
      </div>
    </body>
    </html>
    <?php
} else {
    echo "Invalid reset link.";
}