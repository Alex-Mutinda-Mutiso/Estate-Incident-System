<?php
require dirname(__DIR__, 3) . '/app/bootstrap.php';
require dirname(__DIR__, 3) . '/app/helpers/AccessHelper.php';
require dirname(__DIR__, 3) . '/app/security.php';

AccessHelper::requireRole('resident');

$today = date('Y-m-d');
?>
<!DOCTYPE html>
<html>
<head>
  <title>Submit a Complaint</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="/estate_incident_system/public/css/styles.css">
  <style>
    body { font-family: Arial, sans-serif; background: #f4f6f9; margin: 0; padding: 0; }
    .form-container { max-width: 700px; margin: 40px auto; background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
    h2 { margin-bottom: 20px; color: #333; text-align: center; }
    label { display: block; margin-top: 15px; font-weight: bold; color: #444; }
    select, textarea, input[type="text"], input[type="file"], input[type="date"] {
      width: 100%; padding: 10px; margin-top: 8px; border: 1px solid #ccc; border-radius: 4px; font-size: 14px;
    }
    button { margin-top: 20px; background: #2196f3; color: #fff; border: none; padding: 12px 20px; border-radius: 4px; cursor: pointer; font-size: 16px; width: 100%; }
    button:hover { background: #1976d2; }
    .back-link { display: block; margin-top: 20px; text-align: center; }
    .back-link a { color: #2196f3; text-decoration: none; font-weight: bold; }
    .back-link a:hover { text-decoration: underline; }
  </style>
</head>
<body>
  <div class="form-container">
    <h2>Submit a Complaint</h2>
    <form method="post" action="?route=report_action" enctype="multipart/form-data">
      
      <label>Category:</label>
      <select name="category" required>
        <option value="security">Security</option>
        <option value="maintenance">Maintenance</option>
        <option value="noise">Noise</option>
        <option value="other">Other</option>
      </select>

      <label>Description:</label>
      <textarea name="description" rows="4" placeholder="Describe the issue clearly..." required></textarea>

      <label>Location:</label>
      <input type="text" name="location" placeholder="e.g., House 12, Block A" required>

      <label>Upload Image (optional):</label>
      <input type="file" name="image" accept=".jpg,.jpeg,.png,.gif">

      <label>Date of Incident:</label>
      <input type="date" name="incident_date" max="<?= $today ?>" required>

      <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">

      <button type="submit">Submit Complaint</button>
    </form>

    <div class="back-link">
      <a href="?route=my_reports">← Back to My Reports</a>
    </div>
  </div>
</body>
</html>