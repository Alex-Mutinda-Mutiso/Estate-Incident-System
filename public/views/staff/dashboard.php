<?php
require dirname(__DIR__, 3) . '/app/bootstrap.php';
require dirname(__DIR__, 3) . '/app/helpers/AccessHelper.php';
require dirname(__DIR__, 3) . '/app/security.php';

AccessHelper::requireRole('staff');  

$staff_id = $_SESSION['user_id'];

$stmt = db()->prepare("SELECT * FROM complaints WHERE assigned_to=? ORDER BY created_at DESC");
$stmt->execute([$staff_id]);
$complaints = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
  <title>Staff Dashboard</title>
  <link rel="stylesheet" href="/estate_incident_system/public/css/styles.css">
  <style>
    body {
      font-family: Arial, sans-serif;
      background:#f4f6f9;
      margin:0;
      padding:0;
    }

    header {
      background:#2196f3;
      color:#fff;
      padding:15px;
      display:flex;
      justify-content:space-between;
      align-items:center;
    }
    header h1 { margin:0; }
    nav a {
      color:#fff;
      text-decoration:none;
      margin-left:15px;
      padding:6px 12px;
      background:#1976d2;
      border-radius:4px;
    }
    nav a:hover { background:#0d47a1; }

    .container {
      display: flex;
      justify-content: center;   /* center horizontally */
      align-items: flex-start;
      min-height: calc(100vh - 80px);
      padding: 20px;
    }

    .table-wrapper {
      width: 100%;               /* fluid width */
      max-width: 1000px;         /* cap width so it doesn’t stretch too wide */
      margin: 0 auto;            /* center horizontally */
      background: #fff;
      padding: 20px;
      border-radius: 8px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }

    h2 {
      margin-top:0;
      margin-bottom:20px;
      font-size:1.5rem;
      color:#333;
      text-align:center;         /* center heading */
    }

    table {
      width: 100%;
      border-collapse: collapse;
      font-size: 1.1rem;
    }
    th, td {
      padding: 12px 16px;
      border: 1px solid #ddd;
      text-align: left;
    }
    th {
      background:#2196f3;
      color:#fff;
      font-size: 1.2rem;
    }
    tr:nth-child(even) { background:#f9f9f9; }

    button {
      background:#2196f3;
      color:#fff;
      border:none;
      padding:6px 12px;
      cursor:pointer;
      border-radius:4px;
    }
    button:hover { background:#1976d2; }

    select, input[type=text] {
      padding:6px;
      font-size: 1rem;
    }

    .flash {
      background:#dff0d8;
      color:#3c763d;
      padding:10px;
      margin-bottom:15px;
      border:1px solid #d6e9c6;
      border-radius:6px;
      text-align:center;
    }

    img.thumb {
      max-width:80px;
      border-radius:4px;
    }

    /* ✅ Responsive card layout for mobile */
    @media (max-width: 768px) {
      table, thead, tbody, th, td, tr {
        display: block;
      }
      thead {
        display: none;
      }
      tr {
        margin-bottom: 15px;
        border: 1px solid #ddd;
        border-radius: 6px;
        background: #fff;
        padding: 10px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
      }
      td {
        border: none;
        padding: 8px;
        position: relative;
        text-align: left;
      }
      td::before {
        content: attr(data-label);
        font-weight: bold;
        display: block;
        margin-bottom: 4px;
        color: #2196f3;
      }
    }
  </style>
</head>
<body>
  <header>
    <h1>Estate Incident System - Staff Dashboard</h1>
    <nav>
      <a href="?route=home">Home</a>
      <a href="?route=logout">Logout</a>
    </nav>
  </header>

  <div class="container">
    <div class="table-wrapper">
      <h2>Assigned Complaints</h2>

      <?php if (!empty($_SESSION['flash_message'])): ?>
        <div class="flash"><?= htmlspecialchars($_SESSION['flash_message']) ?></div>
        <?php unset($_SESSION['flash_message']); ?>
      <?php endif; ?>

      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Category</th>
            <th>Description</th>
            <th>Status</th>
            <th>Remarks</th>
            <th>Evidence</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($complaints as $c): ?>
            <tr>
              <td data-label="ID"><?= $c['id'] ?></td>
              <td data-label="Category"><?= ucfirst(htmlspecialchars($c['category'])) ?></td>
              <td data-label="Description"><?= htmlspecialchars($c['description']) ?></td>
              <td data-label="Status"><?= ucfirst(htmlspecialchars($c['status'])) ?></td>
              <td data-label="Remarks"><?= $c['remarks'] ? htmlspecialchars($c['remarks']) : '—' ?></td>
             <td data-label="Evidence">
                 <?php if (!empty($c['image'])): ?>
                 <img src="/estate_incident_system/public/view_upload.php?file=<?= urlencode($c['image']) ?>" class="thumb" alt="Evidence"><br>
                 <a href="/estate_incident_system/public/view_upload.php?file=<?= urlencode($c['image']) ?>" target="_blank">View</a> |
                 <a href="/estate_incident_system/public/view_upload.php?file=<?= urlencode($c['image']) ?>&download=1">Download</a>
                 <?php else: ?>
                     No evidence
                 <?php endif; ?>
              </td>
              <td data-label="Action">
                <form method="post" action="index.php?route=update_status">
                  <input type="hidden" name="id" value="<?= $c['id'] ?>">
                  <select name="status">
                    <option value="pending" <?= ($c['status'] === 'pending') ? 'selected' : '' ?>>Pending</option>
                    <option value="in-progress" <?= ($c['status'] === 'in-progress') ? 'selected' : '' ?>>In Progress</option>
                    <option value="resolved" <?= ($c['status'] === 'resolved') ? 'selected' : '' ?>>Resolved</option>
                  </select>
                  <input type="text" name="remarks" placeholder="Remarks" value="<?= htmlspecialchars($c['remarks']) ?>">
                  <button type="submit">Update</button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</body>
</html>