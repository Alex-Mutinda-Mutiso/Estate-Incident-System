<?php
require dirname(__DIR__, 3) . '/app/bootstrap.php';
require dirname(__DIR__, 3) . '/app/helpers/AccessHelper.php';
require dirname(__DIR__, 3) . '/app/security.php';

AccessHelper::requireRole('resident');
session_start();

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) { die("You must be logged in."); }

$id = $_GET['id'] ?? null;
if (!$id) { die("Invalid report ID."); }

$conn = db();
$stmt = $conn->prepare("SELECT * FROM complaints WHERE id=? AND user_id=? AND status='pending'");
$stmt->execute([$id, $user_id]);
$complaint = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$complaint) { die("Report not found or not editable."); }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category = $_POST['category'];
    $description = $_POST['description'];

    $update = $conn->prepare("UPDATE complaints SET category=?, description=? WHERE id=? AND user_id=?");
    $update->execute([$category, $description, $id, $user_id]);

    $_SESSION['flash_message'] = "Report updated successfully!";
    header("Location: index.php?route=my_reports");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Edit Report</title>
  <link rel="stylesheet" href="/estate_incident_system/public/css/styles.css">
</head>
<body>
  <div class="reports-container">
    <h2>Edit Report</h2>
    <form method="post">
      <label>Category:</label><br>
      <input type="text" name="category" value="<?= htmlspecialchars($complaint['category']) ?>" required><br><br>

      <label>Description:</label><br>
      <textarea name="description" rows="4" required><?= htmlspecialchars($complaint['description']) ?></textarea><br><br>

      <button type="submit" class="btn">Save Changes</button>
      <a href="index.php?route=my_reports" class="btn btn-home">Cancel</a>
    </form>
  </div>
</body>
</html>