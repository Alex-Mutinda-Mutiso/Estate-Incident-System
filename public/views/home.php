<?php
require dirname(__DIR__, 2) . '/app/bootstrap.php';
require dirname(__DIR__, 2) . '/app/security.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Estate Incident System - Home</title>
  <link rel="stylesheet" href="/estate_incident_system/public/css/styles.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    html, body {
      height: 100%;
      margin: 0;
      display: flex;
      flex-direction: column;
      font-family: 'Segoe UI', Arial, sans-serif;
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

    #preloader {
      position: fixed;
      top:0; left:0; right:0; bottom:0;
      background:#121212;
      display:flex;
      justify-content:center;
      align-items:center;
      z-index:9999;
    }
    .spinner {
      border: 8px solid #333;
      border-top: 8px solid #2196f3;
      border-radius: 50%;
      width: 60px;
      height: 60px;
      animation: spin 1s linear infinite;
    }
    @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }

    #main-content {
      display:none;
      opacity:0;
      transition: opacity 1s ease-in;
      flex: 1;
      display: flex;
      flex-direction: column;
    }

    header {
      background:#1e1e1e;
      color:#fff;
      padding:40px 20px;
      text-align:center;
      border-bottom:2px solid #2196f3;
    }
    header h1 { margin:0; font-size:36px; color:#2196f3; }
    header p { margin-top:10px; font-size:18px; color:#ccc; }

    .hero {
      text-align:center;
      padding:80px 20px;
      background:linear-gradient(135deg, #1e1e1e, #2c2c2c);
      color:#fff;
    }
    .hero h2, .hero p {
      opacity:0; transform:translateY(20px);
      animation: fadeUp 1s ease forwards;
    }
    .hero h2 { font-size:40px; margin-bottom:20px; color:#2196f3; animation-delay:0.3s; }
    .hero p { font-size:20px; max-width:700px; margin:0 auto 30px; color:#ccc; animation-delay:0.6s; }
    @keyframes fadeUp { to { opacity:1; transform:translateY(0); } }

    .hero .actions { margin-top:20px; }
    .hero .actions a {
      display:inline-block;
      margin:10px;
      padding:15px 30px;
      font-size:18px;
      background:#2196f3;
      color:#fff;
      text-decoration:none;
      border-radius:8px;
      transition:background 0.3s;
      animation:pulse 2s infinite;
    }
    .hero .actions a:hover { background:#1976d2; }
    @keyframes pulse { 0%{transform:scale(1);} 50%{transform:scale(1.05);} 100%{transform:scale(1);} }

    .features {
      display:flex;
      justify-content:center;
      flex-wrap:wrap;
      margin:60px 20px;
    }
    .feature {
      background:#1e1e1e;
      border:1px solid #333;
      border-radius:8px;
      padding:30px;
      width:300px;
      text-align:center;
      margin:20px;
      box-shadow:0 2px 8px rgba(0,0,0,0.5);
      transition:transform 0.3s, box-shadow 0.3s;
    }
    .feature:hover { transform:translateY(-8px) scale(1.03); box-shadow:0 6px 16px rgba(0,0,0,0.6); }
    .feature h3 { margin-top:10px; color:#2196f3; }
    .feature p { color:#bbb; }
    .feature i { font-size:40px; color:#2196f3; }

    footer {
      background:#1e1e1e;
      color:#888;
      text-align:center;
      padding:20px;
      border-top:1px solid #333;
      margin-top:auto; 
    }
  </style>
</head>
<body>
  <div id="preloader"><div class="spinner"></div></div>

  <div id="main-content">
    <header>
      <h1>Estate Incident System</h1>
      <p>Keeping our community safe and connected</p>
    </header>

    <section class="hero">
      <h2>Welcome to the Estate Incident System</h2>
      <p>Manage incidents, coordinate contractors, and protect our estate with ease.</p>
      <div class="actions">
        <a href="?route=register">Report Incident</a>
      </div>
    </section>

    <section class="features">
      <div class="feature">
        <i class="fas fa-exclamation-circle"></i>
        <h3>Incident Reporting</h3>
        <p>Residents can quickly report issues and track their resolution.</p>
      </div>
      <div class="feature">
        <i class="fas fa-users-cog"></i>
        <h3>Contractor Management</h3>
        <p>Admins can assign contractors or staff to handle cases efficiently.</p>
      </div>
      <div class="feature">
        <i class="fas fa-lock"></i>
        <h3>Secure Login</h3>
        <p>Access your dashboard safely with modern authentication.</p>
      </div>
    </section>

    <footer>
      <p>&copy; <?= date('Y') ?> Estate Incident System. All rights reserved.</p>
    </footer>
  </div>

  <script>
    document.addEventListener("DOMContentLoaded", () => {
      const preloader   = document.getElementById("preloader");
      const mainContent = document.getElementById("main-content");

      setTimeout(() => {
        preloader.style.display = "none";
        mainContent.style.display = "flex"; 
        setTimeout(() => { mainContent.style.opacity = "1"; }, 50);
      }, 2000);
    });
  </script>
</body>
</html>