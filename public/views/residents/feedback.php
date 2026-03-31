<?php
require dirname(__DIR__, 3) . '/app/bootstrap.php';
require dirname(__DIR__, 3) . '/app/helpers/AccessHelper.php';
require dirname(__DIR__, 3) . '/app/security.php';

AccessHelper::requireRole('resident');
session_start();

$user_id = $_SESSION['user_id'] ?? null;
$id = $_GET['id'] ?? null;

$conn = db();
$stmt = $conn->prepare("SELECT * FROM complaints WHERE id=? AND user_id=? AND status='resolved'");
$stmt->execute([$id, $user_id]);
$complaint = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$complaint) { die("Complaint not found or not eligible for feedback."); }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rating = $_POST['rating'];
    $comment = $_POST['comment'];

    $insert = $conn->prepare("INSERT INTO feedback (complaint_id, user_id, rating, comment) VALUES (?, ?, ?, ?)");
    $insert->execute([$id, $user_id, $rating, $comment]);

    $_SESSION['flash_message'] = "Thank you for your feedback!";
    header("Location: index.php?route=my_reports");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Give Feedback</title>
  <link rel="stylesheet" href="/estate_incident_system/public/css/styles.css">
</head>
<body>
  <div class="reports-container">
    <h2>Feedback for Complaint #<?= $complaint['id'] ?></h2>
    <form method="post">
      <label>Rating (1–5):</label><br>
      <select name="rating" required>
        <option value="">Select</option>
        <option value="1">1 - Very Poor</option>
        <option value="2">2 - Poor</option>
        <option value="3">3 - Fair</option>
        <option value="4">4 - Good</option>
        <option value="5">5 - Excellent</option>
      </select><br><br>

      <label>Comments:</label><br>
      <textarea name="comment" rows="4"></textarea><br><br>

      <button type="submit" class="btn">Submit Feedback</button>
      <a href="index.php?route=my_reports" class="btn btn-home">Cancel</a>
    </form>
  </div>
</body>
</html>