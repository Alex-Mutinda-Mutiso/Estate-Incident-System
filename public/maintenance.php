<?php
require __DIR__ . '/../app/bootstrap.php';
$conn = db();

// Fetch dynamic message and email from settings
$stmt = $conn->prepare("SELECT value FROM settings WHERE name = 'maintenance_message'");
$stmt->execute();
$message = $stmt->fetchColumn() ?: "We’re currently performing updates to improve the system.";

$stmt = $conn->prepare("SELECT value FROM settings WHERE name = 'maintenance_email'");
$stmt->execute();
$email = $stmt->fetchColumn() ?: "support@example.com";
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Site Under Maintenance</title>
  <link rel="stylesheet" href="/estate_incident_system/public/css/styles.css">
  <style>
    body {
      font-family: Arial, sans-serif;
      text-align: center;
      padding: 50px;
      background: #f4f6f9;
      color: #333;
    }
    h1 {
      color: #2196f3;
      margin-bottom: 20px;
    }
    p {
      font-size: 1.1em;
      margin-bottom: 15px;
    }
    .notice {
      background: #fff3cd;
      border: 1px solid #ffeeba;
      padding: 20px;
      border-radius: 5px;
      display: inline-block;
    }
    footer {
      margin-top: 40px;
      font-size: 0.9em;
      color: #777;
    }
  </style>
</head>
<body>
  <div class="notice">
    <h1>🚧 Site Under Maintenance</h1>
    <p><?= htmlspecialchars($message) ?></p>
    <p>If urgent, contact us at <a href="mailto:<?= htmlspecialchars($email) ?>"><?= htmlspecialchars($email) ?></a></p>
  </div>

  <footer>
    &copy; <?= date('Y') ?> Estate Incident System. All rights reserved.
  </footer>
</body>
</html>