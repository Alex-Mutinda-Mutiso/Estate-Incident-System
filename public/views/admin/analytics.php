<?php
require dirname(__DIR__, 3) . '/app/bootstrap.php';
require dirname(__DIR__, 3) . '/app/security.php';
session_start();

if ($_SESSION['role'] !== 'admin') {
    header("Location: index.php?route=login");
    exit;
}

$stmt = db()->query("
    SELECT category, COUNT(*) as total
    FROM complaints
    GROUP BY category
");
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

$labels = [];
$counts = [];
foreach ($data as $row) {
    $labels[] = $row['category'];
    $counts[] = $row['total'];
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Analytics Dashboard</title>
  <link rel="stylesheet" href="/estate_incident_system/public/css/styles.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
    .chart-container { width:500px; margin:auto; }
    .legend { margin-top:20px; }
    .legend-item { display:flex; align-items:center; margin-bottom:5px; }
    .legend-color { width:20px; height:20px; margin-right:10px; }
  </style>
</head>
<body>
  <header>
    <h1>Estate Incident System - Analytics</h1>
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
    <h2>Complaints Analytics</h2>
    <div class="chart-container">
      <canvas id="complaintsChart"></canvas>
    </div>

    <div class="legend">
      <div class="legend-item"><div class="legend-color" style="background:#f44336;"></div> Other</div>
      <div class="legend-item"><div class="legend-color" style="background:#4caf50;"></div> Maintenance</div>
      <div class="legend-item"><div class="legend-color" style="background:#ff9800;"></div> Security</div>
      <div class="legend-item"><div class="legend-color" style="background:#2196f3;"></div> Fire</div>
    </div>
  </div>

  <script>
    const ctx = document.getElementById('complaintsChart').getContext('2d');
    const complaintsChart = new Chart(ctx, {
      type: 'pie',
      data: {
        labels: <?php echo json_encode($labels); ?>,
        datasets: [{
          data: <?php echo json_encode($counts); ?>,
          backgroundColor: ['#4caf50','#f44336','#ff9800','#2196f3']
        }]
      },
      options: {
        responsive: true,
        plugins: {
          legend: { display: false } 
        }
      }
    });
  </script>
</body>
</html>