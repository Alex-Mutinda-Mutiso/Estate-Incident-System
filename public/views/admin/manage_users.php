<?php
require dirname(__DIR__, 3) . '/app/bootstrap.php';
require dirname(__DIR__, 3) . '/app/security.php';
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php?route=login");
    exit;
}

$users = db()->query("SELECT id, name, email, role FROM users ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
  <title>Manage Users</title>
  <link rel="stylesheet" href="/estate_incident_system/public/css/styles.css">
  <style>
    body { font-family: Arial, sans-serif; margin:0; padding:0; background:#f4f6f9; }
    header { background:#2196f3; color:#fff; padding:15px; position: fixed; top:0; left:0; right:0; z-index:1000; }
    header h1 { margin:0; }
    .sidebar { width:200px; background:#333; color:#fff; position:fixed; top:60px; left:0; bottom:0; padding-top:20px; }
    .sidebar ul { list-style:none; padding:0; }
    .sidebar li { margin:15px 0; }
    .sidebar a { color:#fff; text-decoration:none; display:block; padding:10px; }
    .sidebar a:hover { background:#2196f3; }
    .container { margin-left:220px; margin-top:80px; padding:20px; }
    table { width:100%; border-collapse:collapse; background:#fff; }
    th, td { padding:10px; border:1px solid #ddd; text-align:left; }
    th { background:#2196f3; color:#fff; }
    tr:nth-child(even) { background:#f9f9f9; }
    button { background:#2196f3; color:#fff; border:none; padding:5px 10px; cursor:pointer; }
    button:hover { background:#1976d2; }
    select { padding:5px; }
    .flash { background:#dff0d8; color:#3c763d; padding:10px; margin-bottom:15px; border:1px solid #d6e9c6; }
    .add-btn { background:#4CAF50; color:#fff; border:none; display: block; margin: 0; padding:8px 12px; cursor:pointer; font-size:14px; }
    .add-btn:hover { background:#388E3C; }
  </style>
</head>
<body>
  <header>
    <h1>Estate Incident System - Manage Users</h1>
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
    <h2>User Accounts</h2>

    <?php if (!empty($_SESSION['flash_message'])): ?>
      <div class="flash"><?= htmlspecialchars($_SESSION['flash_message']) ?></div>
      <?php unset($_SESSION['flash_message']); ?>
    <?php endif; ?>

    <div style="margin-bottom:15px;">
      <a href="index.php?route=add_user" style="text-decoration:none;">
        <button class="add-btn">+ Add User</button>
      </a>
    </div>

    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>Name</th>
          <th>Email</th>
          <th>Role</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($users as $u): ?>
          <tr>
            <td><?= $u['id'] ?></td>
            <td><?= htmlspecialchars($u['name']) ?></td>
            <td><?= htmlspecialchars($u['email']) ?></td>
            <td><?= htmlspecialchars($u['role']) ?></td>
            <td>
              <form method="post" action="index.php?route=update_user" style="display:inline;">
                <input type="hidden" name="id" value="<?= $u['id'] ?>">
                <select name="role" required>
                  <option value="resident" <?= ($u['role'] === 'resident') ? 'selected' : '' ?>>Resident</option>
                  <option value="staff" <?= ($u['role'] === 'staff') ? 'selected' : '' ?>>Staff</option>
                  <option value="admin" <?= ($u['role'] === 'admin') ? 'selected' : '' ?>>Admin</option>
                </select>
                <button type="submit" name="action" value="edit">Save</button>
              </form>

              <form method="post" action="index.php?route=update_user" style="display:inline;">
                <input type="hidden" name="id" value="<?= $u['id'] ?>">
                <button type="submit" name="action" value="delete">Delete</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</body>
</html>