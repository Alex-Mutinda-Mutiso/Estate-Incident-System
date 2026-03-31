<div class="nav">
  <?php if (!empty($_SESSION['user_id'])): ?>
    <?php if ($_SESSION['role'] === 'resident'): ?>
      <a href="?route=report">Submit Complaint</a>
      <a href="?route=my_reports">My Reports</a>
    <?php elseif ($_SESSION['role'] === 'staff'): ?>
      <a href="?route=staff_dashboard">Staff Dashboard</a>
    <?php elseif ($_SESSION['role'] === 'admin'): ?>
      <a href="?route=admin_dashboard">Admin Dashboard</a>
      <a href="views/admin/manage_users.php">Manage Users</a>
      <a href="views/admin/analytics.php">Analytics</a>
    <?php endif; ?>
    <a href="logout.php">Logout</a>
  <?php else: ?>
    <a href="?route=login">Login</a>
    <a href="?route=register">Register</a>
  <?php endif; ?>
</div>