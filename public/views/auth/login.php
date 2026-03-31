<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login - Estate Incident System</title>
  <link rel="stylesheet" href="/estate_incident_system/public/css/styles.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    body {
      font-family: 'Segoe UI', Arial, sans-serif;
      margin:0; padding:0;
      height:100vh;
      display:flex;
      justify-content:center;
      align-items:center;
      background: linear-gradient(-45deg, #0f2027, #203a43, #2c5364, #1c1c1c);
      background-size: 400% 400%;
      animation: gradientBG 15s ease infinite;
      color:#e0e0e0;
    }
    @keyframes gradientBG {
      0% {background-position:0% 50%;}
      50% {background-position:100% 50%;}
      100% {background-position:0% 50%;}
    }
    .login-box {
      background: rgba(30,30,30,0.9); 
      backdrop-filter: blur(12px);
      border-radius: 12px;
      padding: 40px;
      width: 380px;
      box-shadow: 0 8px 24px rgba(0,0,0,0.6);
      text-align:center;
      animation: fadeIn 1.2s ease forwards;
    }
    @keyframes fadeIn {
      from {opacity:0; transform:translateY(20px);}
      to {opacity:1; transform:translateY(0);}
    }
    .login-box h2 {
      margin-bottom:20px;
      font-size:28px;
      color:#21cbf3;
    }
    .flash {
      background:#f8d7da;
      color:#721c24;
      padding:10px;
      margin-bottom:15px;
      border:1px solid #f5c6cb;
      border-radius:6px;
      text-align:center;
    }
    .success {
      background:#d4edda;
      color:#155724;
      padding:10px;
      margin-bottom:15px;
      border:1px solid #c3e6cb;
      border-radius:6px;
      text-align:center;
    }
    .login-box form {
      display:flex;
      flex-direction:column;
      gap:15px;
    }
    input {
      width:100%;
      padding:12px;
      border:none;
      border-radius:8px;
      background:rgba(255,255,255,0.08);
      color:#fff;
      outline:none;
      transition:background 0.3s, box-shadow 0.3s;
    }
    input:focus {
      background:rgba(255,255,255,0.15);
      box-shadow:0 0 8px #21cbf3;
    }
    .options {
      display:flex;
      justify-content:space-between;
      align-items:center;
      font-size:14px;
      color:#ccc;
    }
    .options a {
      color:#21cbf3;
      text-decoration:none;
    }
    .options a:hover { text-decoration:underline; }
    button {
      padding:12px;
      border:none;
      border-radius:8px;
      background:#2196f3;
      color:#fff;
      font-size:16px;
      cursor:pointer;
      transition:background 0.3s, transform 0.2s;
    }
    button:hover {
      background:#1976d2;
      transform:scale(1.05);
    }
    .google-btn {
      display:inline-block;
      background:#db4437;
      color:#fff;
      padding:12px;
      border-radius:8px;
      text-decoration:none;
      font-weight:bold;
      margin-top:15px;
      transition:background 0.3s, transform 0.2s;
    }
    .google-btn:hover {
      background:#c23321;
      transform:scale(1.05);
    }
    .signup {
      margin-top:20px;
      font-size:14px;
      color:#ccc;
    }
    .signup a {
      color:#21cbf3;
      text-decoration:none;
      font-weight:bold;
    }
    .signup a:hover { text-decoration:underline; }
  </style>
</head>
<body>
  <div class="login-box">
    <h2><i class="fas fa-sign-in-alt"></i> Login</h2>

    <?php if (!empty($_SESSION['flash_message'])): ?>
      <div class="flash" role="alert"><?= htmlspecialchars($_SESSION['flash_message']) ?></div>
      <?php unset($_SESSION['flash_message']); ?>
    <?php endif; ?>

    <?php if (isset($_GET['success']) && $_GET['success'] === 'logged_out'): ?>
      <div class="success" role="alert">✅ You have been logged out successfully.</div>
    <?php endif; ?>

    <?php if (isset($_GET['error']) && $_GET['error'] === 'session_expired'): ?>
      <div class="flash" role="alert">⚠️ Your session expired due to inactivity. Please log in again.</div>
    <?php endif; ?>

    <form method="post" action="index.php?route=login_action">
      <input type="text" name="username" placeholder="Username or Email" required>
      <input type="password" name="password" placeholder="Password" required>

      <div class="options">
        <label><input type="checkbox" name="remember"> Remember me</label>
        <a href="index.php?route=forgot_password">Forgot Password?</a>
      </div>


      <button type="submit">LOGIN</button>
    </form>

    <a href="views/auth/google_login.php" class="google-btn">Sign in with Google</a>

    <div class="signup">
      Don’t have an account? <a href="index.php?route=register">Sign Up</a>
    </div>
  </div>

  <script>
    setTimeout(() => {
      document.querySelectorAll('.flash, .success').forEach(el => {
        el.style.transition = "opacity 1s";
        el.style.opacity = "0";
        setTimeout(() => el.remove(), 1000);
      });
    }, 5000);
  </script>
</body>
</html>