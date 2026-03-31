<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Register - Estate Incident System</title>
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

    .register-box {
      background: rgba(30,30,30,0.9);
      backdrop-filter: blur(12px);
      border-radius: 12px;
      padding: 40px;
      width: 380px;
      box-shadow: 0 8px 24px rgba(0,0,0,0.6);
      text-align:center;
      animation: fadeIn 1.2s ease forwards;
    }
    @keyframes fadeIn { from {opacity:0; transform:translateY(20px);} to {opacity:1; transform:translateY(0);} }

    .register-box h2 {
      margin-bottom:20px;
      font-size:28px;
      color:#21cbf3;
    }

    .register-box form {
      display:flex;
      flex-direction:column;
      gap:15px;
    }

    .input-group {
      position:relative;
    }
    .input-group i {
      position:absolute;
      left:12px;
      top:50%;
      transform:translateY(-50%);
      color:#21cbf3;
    }
    .input-group input {
      width:100%;
      padding:12px 12px 12px 40px; 
      border:none;
      border-radius:8px;
      background:rgba(255,255,255,0.08);
      color:#fff;
      outline:none;
      transition:background 0.3s, box-shadow 0.3s;
    }
    .input-group input:focus {
      background:rgba(255,255,255,0.15);
      box-shadow:0 0 8px #21cbf3;
    }

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
  <div class="register-box">
    <h2><i class="fas fa-user-plus"></i> Register</h2>
    <form method="post" action="?route=register_action">
      <div class="input-group">
        <i class="fas fa-user"></i>
        <input type="text" name="name" placeholder="Full Name" required>
      </div>
      <div class="input-group">
        <i class="fas fa-envelope"></i>
        <input type="email" name="email" placeholder="Email Address" required>
      </div>
      <div class="input-group">
        <i class="fas fa-lock"></i>
        <input type="password" name="password" placeholder="Password" required>
      </div>
      <div class="input-group">
        <i class="fas fa-home"></i>
        <input type="text" name="estate_unit" placeholder="Estate Unit" required>
      </div>
      <div class="input-group">
        <i class="fas fa-door-closed"></i>
        <input type="text" name="house_number" placeholder="House Number" required>
      </div>
      <div class="input-group">
        <i class="fas fa-building"></i>
        <input type="text" name="block_number" placeholder="Block Number" required>
      </div>
      <button type="submit">REGISTER</button>
    </form>
    <div class="signup">
      Already have an account? <a href="?route=login">Login</a><br>
      Are you staff? <a href="?route=staff_register">Register here</a>
    </div>
  </div>
</body>
</html>