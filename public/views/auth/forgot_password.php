<!DOCTYPE html>
<html>
<head>
  <title>Forgot Password</title>
  <link rel="stylesheet" href="/estate_incident_system/public/css/styles.css">
</head>
<body>
  <div class="login-container">
    <div class="login-box">
      <div class="icon">🔑</div>
      <h2>Forgot Password</h2>
      <form method="post" action="?route=forgot_password_action">
        <input type="email" name="email" placeholder="Enter your email" required>
        <button type="submit">Reset Password</button>
      </form>
    </div>
  </div>
</body>
</html>