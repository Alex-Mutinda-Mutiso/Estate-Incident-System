<?php
require dirname(__DIR__, 3) . '/app/bootstrap.php';
require dirname(__DIR__, 3) . '/app/security.php';
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    error_log("Unauthorized admin dashboard access attempt from IP: " . $_SERVER['REMOTE_ADDR']);
    die("Access denied. Only administrators can access this dashboard.");
}

$search = $_GET['q'] ?? '';

if ($search) {
    $stmt = db()->prepare("
        SELECT c.*, 
               u.name AS resident_name, 
               s.name AS staff_name,
               f.comment AS feedback
        FROM complaints c
        LEFT JOIN users u ON c.user_id = u.id
        LEFT JOIN users s ON c.assigned_to = s.id
        LEFT JOIN feedback f ON c.id = f.complaint_id
        WHERE c.category LIKE ? 
           OR c.description LIKE ? 
           OR c.status LIKE ? 
           OR u.name LIKE ?
           OR f.comment LIKE ?
        ORDER BY c.created_at DESC
    ");
    $like = "%$search%";
    $stmt->execute([$like, $like, $like, $like, $like]);
} else {
    $stmt = db()->query("
        SELECT c.*, 
               u.name AS resident_name, 
               s.name AS staff_name,
               f.comment AS feedback
        FROM complaints c
        LEFT JOIN users u ON c.user_id = u.id
        LEFT JOIN users s ON c.assigned_to = s.id
        LEFT JOIN feedback f ON c.id = f.complaint_id
        ORDER BY c.created_at DESC
    ");
}
$complaints = $stmt->fetchAll(PDO::FETCH_ASSOC);

$staff = db()->query("SELECT id, name FROM users WHERE role = 'staff'")->fetchAll(PDO::FETCH_ASSOC);

$statusOptions = ['Pending', 'In Progress', 'Resolved'];
?>
<!DOCTYPE html>
<html>
<head>
  <title>Admin Dashboard</title>
  <link rel="stylesheet" href="/estate_incident_system/public/css/styles.css">
  <style>
    body { font-family: Arial, sans-serif; margin:0; padding:0; background:#f4f6f9; }
    header {
      background:#2196f3; color:#fff; padding:15px;
      position: fixed; top:0; left:0; right:0;
      z-index: 1000; display:flex; justify-content:space-between; align-items:center;
    }
    header h1 { margin:0; }
    .menu-toggle { display:none; background:none; border:none; font-size:24px; color:#fff; cursor:pointer; }

    .sidebar {
      width: 200px; background: #333; color: #fff;
      position: fixed; top: 60px; left: 0; bottom: 0;
      padding-top: 20px; transition: transform 0.3s ease;
    }
    .sidebar ul { list-style: none; padding: 0; }
    .sidebar li { margin: 15px 0; }
    .sidebar a {
      color: #fff; text-decoration: none; display: block; padding: 10px;
    }
    .sidebar a:hover { background: #2196f3; }

    .container { margin-left: 220px; margin-top: 80px; padding: 20px; }

    table { width:100%; border-collapse:collapse; background:#fff; }
    th, td { padding:10px; border:1px solid #ddd; text-align:left; }
    th { background:#2196f3; color:#fff; }
    tr:nth-child(even) { background:#f9f9f9; }

    .flash { background:#dff0d8; color:#3c763d; padding:10px; margin-bottom:15px; border:1px solid #d6e9c6; }

    form { display:inline-block; margin-bottom:5px; }
    button { background:#2196f3; color:#fff; border:none; padding:5px 10px; cursor:pointer; border-radius:4px; }
    button:hover { background:#1976d2; }
    .btn-danger { background:#e53935; }
    .btn-danger:hover { background:#c62828; }
    select { padding:5px; border-radius:4px; border:1px solid #ccc; }
    img.thumb { max-width:80px; border-radius:4px; }

    .search-form { margin-bottom: 20px; display: flex; gap: 10px; }
    .search-form input[type="text"] {
      flex: 1; padding: 8px; border: 1px solid #ddd; border-radius: 4px;
    }
    .search-form .btn {
      background: #2196f3; color: #fff; border: none; padding: 8px 12px;
      border-radius: 4px; cursor: pointer;
    }
    .search-form .btn:hover { background:#1976d2; }

    @media (max-width: 768px) {
      .sidebar { transform: translateX(-100%); }
      .sidebar.active { transform: translateX(0); }
      .container { margin-left: 0; }
      .menu-toggle { display:block; }
    }
  </style>
</head>
<body> 
  <header>
    <h1>Estate Incident System - Admin Dashboard</h1>
    <button class="menu-toggle">☰</button>
  </header>

  <nav class="sidebar" id="sidebar">
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
    <h2>Complaints Overview</h2>

    <?php if (!empty($_SESSION['flash_message'])): ?>
      <div class="flash"><?= htmlspecialchars($_SESSION['flash_message']) ?></div>
      <?php unset($_SESSION['flash_message']); ?>
    <?php endif; ?>

    <form method="get" action="index.php" class="search-form">
      <input type="hidden" name="route" value="admin_dashboard">
      <input type="text" name="q" placeholder="Search complaints..."  
             value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
      <button type="submit" class="btn">Search</button>
    </form>

    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>Resident</th>
          <th>Category</th>
          <th>Description</th>
          <th>Status</th>
          <th>Assigned To</th>
          <th>Evidence</th>
          <th>Feedback</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($complaints as $c): ?>
          <tr>
            <td><?= $c['id'] ?></td>
            <td><?= htmlspecialchars($c['resident_name']) ?></td>
            <td><?= htmlspecialchars($c['category']) ?></td>
            <td><?= htmlspecialchars($c['description']) ?></td>
            <td><?= htmlspecialchars($c['status']) ?></td>
            <td><?= htmlspecialchars($c['staff_name']) ?></td>
            <td>
              <?php if (!empty($c['image'])): ?>
                <img src="view_upload.php?file=<?= urlencode($c['image']) ?>" class="thumb" alt="Evidence"><br>
                <a href="view_upload.php?file=<?= urlencode($c['image']) ?>" target="_blank">View</a> |
                <a href="view_upload.php?file=<?= urlencode($c['image']) ?>&download=1">Download</a>
              <?php else: ?>
                No evidence
              <?php endif; ?>
            </td>
            <td>
              <?= !empty($c['feedback']) ? htmlspecialchars($c['feedback']) : 'No feedback yet' ?>
            </td>
            <td>
              <form method="post" action="index.php?route=assign_case" style="margin-bottom:5px;">
                <input type="hidden" name="id" value="<?= $c['id'] ?>">
                <select name="staff_id">
                  <?php foreach ($staff as $s): ?>
                    <option value="<?= $s['id'] ?>" <?= ($c['assigned_to'] == $s['id']) ? 'selected' : '' ?>>
                      <?= htmlspecialchars($s['name']) ?>
                    </option>
                  <?php endforeach; ?>
                </select>
                <button type="submit" name="action" value="assign">Assign</button>
              </form>

                           <form method="post" action="index.php?route=assign_case" style="margin-bottom:5px;">
                <input type="hidden" name="id" value="<?= $c['id'] ?>">
                <select name="status">
                  <?php foreach ($statusOptions as $status): ?>
                    <option value="<?= $status ?>" <?= ($c['status'] === $status) ? 'selected' : '' ?>>
                      <?= $status ?>
                    </option>
                  <?php endforeach; ?>
                </select>
                <button type="submit" name="action" value="update_status">Update Status</button>
              </form>

              <form method="post" action="index.php?route=delete_case" 
                    onsubmit="return confirm('Are you sure you want to delete this case?');">
                <input type="hidden" name="id" value="<?= $c['id'] ?>">
                <button type="submit" class="btn btn-danger">Delete</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</body>
</html>