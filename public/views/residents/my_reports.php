<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require dirname(__DIR__, 3) . '/app/bootstrap.php';
require dirname(__DIR__, 3) . '/app/helpers/AccessHelper.php';
require dirname(__DIR__, 3) . '/app/security.php';

AccessHelper::requireRole('resident');

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    die("You must be logged in to view this page.");
}

$conn = db();

$statusFilter = $_GET['status'] ?? '';

if ($statusFilter) {
    $stmt = $conn->prepare("SELECT * FROM complaints WHERE user_id=? AND status=? ORDER BY created_at DESC");
    $stmt->execute([$user_id, $statusFilter]);
} else {
    $stmt = $conn->prepare("SELECT * FROM complaints WHERE user_id=? ORDER BY created_at DESC");
    $stmt->execute([$user_id]);
}

$complaints = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
  <title>My Reports</title>
  <link rel="stylesheet" href="/estate_incident_system/public/css/styles.css">
  <style>
    body { font-family: Arial, sans-serif; background: #f4f6f9; margin: 0; padding: 0; }
    .reports-container { max-width: 900px; margin: 40px auto; background: #fff; padding: 25px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
    h2 { margin-bottom: 20px; color: #333; }
    .success-banner { background: #4caf50; color: #fff; padding: 12px; border-radius: 6px; margin-bottom: 20px; font-weight: bold; text-align: center; box-shadow: 0 2px 6px rgba(0,0,0,0.15); }
    table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
    table th, table td { border: 1px solid #ddd; padding: 10px; text-align: left; }
    table th { background: #2196f3; color: #fff; }
    .btn { display: inline-block; padding: 8px 12px; background: #2196f3; color: #fff; text-decoration: none; border-radius: 4px; transition: background 0.3s ease; font-size: 14px; }
    .btn:hover { background: #1976d2; }
    .btn-home { background: #4caf50; }
    .btn-home:hover { background: #388e3c; }
    .btn-resend { background: #ff9800; }
    .btn-resend:hover { background: #e68900; }
    .btn-edit { background: #673ab7; }
    .btn-edit:hover { background: #512da8; }
    .btn-feedback { background: #009688; }
    .btn-feedback:hover { background: #00796b; }
    .no-reports { color: #666; font-style: italic; }
    .filter-form { margin-bottom: 20px; }
    .status-badge { padding: 5px 10px; border-radius: 4px; color: #fff; font-weight: bold; }
    .status-pending { background: #f44336; }
    .status-in_progress { background: #ff9800; }
    .status-resolved { background: #4caf50; }
    .button-row { display: flex; justify-content: space-between; margin-top: 20px; }
    select { color: #000; background: #fff; }
    img.thumb { max-width: 80px; border-radius: 4px; }
  </style>
</head>
<body>
  <div class="reports-container">
    <h2>My Reports</h2>

    <?php if (isset($_SESSION['flash_message'])): ?>
      <div class="success-banner">
        <?= htmlspecialchars($_SESSION['flash_message']) ?>
      </div>
      <?php unset($_SESSION['flash_message']); ?>
    <?php endif; ?>

    <?php if (isset($_GET['success']) && $_GET['success'] === 'report_submitted'): ?>
      <div class="success-banner">
        ✅ Report submitted successfully!
      </div>
    <?php endif; ?>

    <form method="get" action="index.php" class="filter-form">
      <input type="hidden" name="route" value="my_reports">
      <label for="status">Filter by status:</label>
      <select name="status" id="status">
        <option value="">All</option>
        <option value="pending" <?= $statusFilter==='pending'?'selected':'' ?>>Pending</option>
        <option value="in_progress" <?= $statusFilter==='in_progress'?'selected':'' ?>>In Progress</option>
        <option value="resolved" <?= $statusFilter==='resolved'?'selected':'' ?>>Resolved</option>
      </select>
      <button type="submit" class="btn">Filter</button>
    </form>

    <?php if (empty($complaints)): ?>
      <p class="no-reports">You have not submitted any reports yet.</p>
    <?php else: ?>
      <table>
        <thead>
          <tr>
            <th>Category</th>
            <th>Description</th>
            <th>Status</th>
            <th>Remarks</th>
            <th>Date</th>
            <th>Evidence</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($complaints as $c): ?>
            <tr>
              <td><?= ucfirst(htmlspecialchars($c['category'])) ?></td>
              <td><?= htmlspecialchars($c['description']) ?></td>
              <td>
                <span class="status-badge status-<?= htmlspecialchars($c['status']) ?>">
                  <?= ucfirst(htmlspecialchars($c['status'])) ?>
                </span>
              </td>
              <td><?= $c['remarks'] ? htmlspecialchars($c['remarks']) : '—' ?></td>
              <td><?= $c['created_at'] ?></td>
              <td>
                <?php if (!empty($c['image'])): ?>
                  <img src="view_upload.php?file=<?= urlencode($c['image']) ?>" class="thumb" alt="Evidence"><br>
                  <a href="view_upload.php?file=<?= urlencode($c['image']) ?>" target="_blank">View</a> |
                  <a href="view_upload.php?file=<?= urlencode($c['image']) ?>&download=1">Download</a>
                <?php else: ?>
                  No Image
                <?php endif; ?>
              </td>
              <td>
                <?php if ($c['status'] === 'pending'): ?>
                  <a href="index.php?route=edit_report&id=<?= $c['id'] ?>" class="btn btn-edit">Edit</a>
                <?php endif; ?>

                <?php if ($c['status'] !== 'resolved'): ?>
                  <form method="post" action="index.php?route=resend_alert" style="display:inline;">
                    <input type="hidden" name="complaint_id" value="<?= $c['id'] ?>">
                    <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">
                    <button type="submit" class="btn btn-resend">Resend Alert</button>
                  </form>
                <?php else: ?>
                  <a href="index.php?route=feedback&id=<?= $c['id'] ?>" class="btn btn-feedback">Give Feedback</a>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>

    <div class="button-row">
      <a href="?route=report" class="btn">Submit New Report</a>
      <a href="?route=home" class="btn btn-home">Home</a>
    </div>
  </div>
</body>
</html>