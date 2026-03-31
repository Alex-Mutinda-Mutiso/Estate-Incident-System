<?php
require dirname(__DIR__, 3) . '/app/bootstrap.php';
require dirname(__DIR__, 3) . '/app/security.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require dirname(__DIR__, 3) . '/vendor/autoload.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php?route=login");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $message    = $_POST['message'] ?? '';
    $recipients = $_POST['recipients'] ?? [];

    $emails = [];

    if (in_array('residents', $recipients)) {
        $stmt = db()->query("SELECT email FROM users WHERE role = 'resident'");
        $emails = array_merge($emails, $stmt->fetchAll(PDO::FETCH_COLUMN));
    }

    if (in_array('contractors', $recipients)) {
        $stmt = db()->query("SELECT email FROM contractors");
        $emails = array_merge($emails, $stmt->fetchAll(PDO::FETCH_COLUMN));
    }

    if (in_array('admins', $recipients)) {
        $stmt = db()->query("SELECT email FROM users WHERE role = 'admin'");
        $emails = array_merge($emails, $stmt->fetchAll(PDO::FETCH_COLUMN));
    }

    foreach ($emails as $email) {
        try {
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'sanchezaleko001@gmail.com'; 
            $mail->Password   = 'whwp odga lywy sqkv';       
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            $mail->setFrom('alerts@estate.com', 'Estate Alerts');
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = 'Estate Emergency Alert';
            $mail->Body    = nl2br(htmlspecialchars($message));

            $mail->send();
        } catch (Exception $e) {
            error_log("Alert email failed for {$email}: {$mail->ErrorInfo}");
        }
    }

    $stmt = db()->prepare("INSERT INTO alerts (message, recipients) VALUES (?, ?)");
    $stmt->execute([$message, implode(',', $recipients)]);

    $_SESSION['flash_message'] = "Emergency alert sent and logged successfully!";
    header("Location: index.php?route=alerts");
    exit;
}

$pastAlerts = db()->query("SELECT * FROM alerts ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
  <title>Emergency Alerts</title>
  <link rel="stylesheet" href="/estate_incident_system/public/css/styles.css">
  <style>
    body { font-family: Arial, sans-serif; margin:0; padding:0; background:#f4f6f9; }
    header {
      background:#2196f3; color:#fff; padding:15px;
      position: fixed; top:0; left:0; right:0; z-index:1000;
    }
    header h1 { margin:0; }
    .sidebar {
      width:200px; background:#333; color:#fff;
      position:fixed; top:60px; left:0; bottom:0; padding-top:20px;
    }
    .sidebar ul { list-style:none; padding:0; }
    .sidebar li { margin:15px 0; }
    .sidebar a { color:#fff; text-decoration:none; display:block; padding:10px; }
    .sidebar a:hover { background:#2196f3; }
    .container { margin-left:220px; margin-top:80px; padding:20px; }
    .flash { background:#dff0d8; color:#3c763d; padding:10px; margin-bottom:15px; border:1px solid #d6e9c6; }
    textarea { width:100%; display: block; margin:0; height:120px; padding:10px; margin-bottom:15px; }
    .recipients { margin-bottom:15px; }
    .send-btn {
      background:#e53935; color:#fff; border:none;
      padding:10px 20px; font-size:16px; cursor:pointer;
    }
    .send-btn:hover { background:#c62828; }
    .preview { background:#fff; border:1px solid #ddd; padding:15px; margin-top:20px; }
    table { width:100%; border-collapse:collapse; background:#fff; margin-top:20px; }
    th, td { padding:10px; border:1px solid #ddd; text-align:left; }
    th { background:#2196f3; color:#fff; }
    tr:nth-child(even) { background:#f9f9f9; }
    form {  max-width: 600px; text-align: left;}

  </style>
</head>
<body>
  <header>
    <h1>Estate Incident System - Emergency Alerts</h1>
  </header>

  <nav class="sidebar">
    <ul>
      <li><a href="?route=admin_dashboard">Dashboard</a></li>
      <li><a href="?route=manage_users">Manage Users</a></li>
      <li><a href="?route=analytics">Analytics</a></li>
      <li><a href="?route=alerts">Alerts</a></li>
      <li><a href="?route=contractors">Contractors</a></li>
      <li><a href="?route=logout">Logout</a></li>
    </ul>
  </nav>

  <div class="container">
    <h2>Emergency Alert Interface</h2>

    <?php if (!empty($_SESSION['flash_message'])): ?>
      <div class="flash"><?= htmlspecialchars($_SESSION['flash_message']) ?></div>
      <?php unset($_SESSION['flash_message']); ?>
    <?php endif; ?>

    <form method="post">
      <label for="message">Alert Message:</label><br>
      <textarea name="message" id="message" placeholder="Type your emergency alert here..."></textarea>

      <div class="recipients">
        <strong>Send to:</strong><br>
        <label><input type="checkbox" name="recipients[]" value="residents" checked> Residents</label><br>
        <label><input type="checkbox" name="recipients[]" value="contractors"> Contractors</label><br>
        <label><input type="checkbox" name="recipients[]" value="admins"> Admins</label>
      </div>

      <button type="submit" class="send-btn">Send Alert</button>
    </form>

    <div class="preview">
      <h3>Preview</h3>
      <p>(This is an emergency alert message to all residents. Example: "There are thugs roaming around, be careful while walking at night.")</p>
    </div>

    <div class="history">
      <h3>Past Alerts</h3>
      <table>
        <tr><th>ID</th><th>Message</th><th>Recipients</th><th>Sent At</th></tr>
        <?php foreach ($pastAlerts as $alert): ?>
          <tr>
            <td><?= $alert['id'] ?></td>
            <td><?= htmlspecialchars($alert['message']) ?></td>
            <td><?= htmlspecialchars($alert['recipients']) ?></td>
            <td><?= $alert['created_at'] ?></td>
          </tr>
        <?php endforeach; ?>
      </table>
    </div>
  </div>
</body>
</html>