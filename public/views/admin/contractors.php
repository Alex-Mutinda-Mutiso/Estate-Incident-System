<?php
require dirname(__DIR__, 3) . '/app/bootstrap.php';
require dirname(__DIR__, 3) . '/app/security.php';
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php?route=login");
    exit;
}

$contractors = db()->query("SELECT id, name, specialty FROM contractors")->fetchAll(PDO::FETCH_ASSOC);

$staff = db()->query("SELECT id, name, role FROM users WHERE role = 'staff'")->fetchAll(PDO::FETCH_ASSOC);

$assignable = array_merge($contractors, $staff);

$stmt = db()->query("
    SELECT c.id, u.name AS resident_name, c.status,
           con.name AS contractor_name, con.specialty,
           u2.name AS staff_name, u2.role AS staff_role
    FROM complaints c
    LEFT JOIN users u ON c.user_id = u.id
    LEFT JOIN contractors con ON c.contractor_id = con.id
    LEFT JOIN users u2 ON c.assigned_to = u2.id
    ORDER BY c.created_at DESC
");
$cases = $stmt->fetchAll(PDO::FETCH_ASSOC);

$statusOptions = ['Pending', 'In Progress', 'Completed'];
?>
<!DOCTYPE html>
<html>
<head>
  <title>Contractors Page</title>
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
    table { width:100%; border-collapse:collapse; background:#fff; }
    th, td { padding:10px; border:1px solid #ddd; text-align:left; }
    th { background:#2196f3; color:#fff; }
    tr:nth-child(even) { background:#f9f9f9; }
    form { display:inline-block; }
    button { background:#2196f3; color:#fff; border:none; padding:5px 10px; cursor:pointer; }
    button:hover { background:#1976d2; }
    select { padding:5px; }
  </style>
</head>
<body>
  <header>
    <h1>Estate Incident System - Contractors</h1>
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
    <h2>Active Cases</h2>
    <table>
      <thead>
        <tr>
          <th>Case ID</th>
          <th>Resident</th>
          <th>Assigned To</th>
          <th>Specialty/Role</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($cases as $c): ?>
          <tr>
            <td><?= $c['id'] ?></td>
            <td><?= htmlspecialchars($c['resident_name']) ?></td>
            <td><?= htmlspecialchars($c['contractor_name'] ?: $c['staff_name']) ?></td>
            <td><?= htmlspecialchars($c['specialty'] ?: $c['staff_role']) ?></td>
            <td><?= htmlspecialchars($c['status']) ?></td>
            <td>
              <form method="post" action="index.php?route=assign_case">                <input type="hidden" name="id" value="<?= $c['id'] ?>">
                <select name="assigned_to">
                  <?php foreach ($assignable as $person): ?>
                    <option value="<?= $person['id'] ?>"
                      <?= ($c['contractor_name'] == $person['name'] || $c['staff_name'] == $person['name']) ? 'selected' : '' ?>>
                      <?= htmlspecialchars($person['name']) ?>
                      <?php if (isset($person['specialty'])): ?>
                        (<?= htmlspecialchars($person['specialty']) ?>)
                      <?php elseif (isset($person['role'])): ?>
                        (<?= htmlspecialchars($person['role']) ?>)
                      <?php endif; ?>
                    </option>
                  <?php endforeach; ?>
                </select>
                <button type="submit" name="action" value="assign">Assign</button>
              </form>

              <form method="post" action="index.php?route=assign_case">                <input type="hidden" name="id" value="<?= $c['id'] ?>">
                <select name="status">
                  <?php foreach ($statusOptions as $status): ?>
                    <option value="<?= $status ?>" <?= ($c['status'] === $status) ? 'selected' : '' ?>>
                      <?= $status ?>
                    </option>
                  <?php endforeach; ?>
                </select>
                <button type="submit" name="action" value="update_status">Update Status</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</body>
</html>

