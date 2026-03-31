<?php
require dirname(__DIR__, 3) . '/app/bootstrap.php';
$conn = db();

if (!isset($_SESSION['role']) || strtolower($_SESSION['role']) !== 'dev') {
    error_log("Unauthorized dev dashboard access attempt from IP: " . $_SERVER['REMOTE_ADDR']);
    die("Access denied.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['maintenance_mode'])) {
        $stmt = $conn->prepare("UPDATE settings SET value = ? WHERE name = 'maintenance_mode'");
        $stmt->execute([$_POST['maintenance_mode']]);
    }
    if (isset($_POST['maintenance_message'])) {
        $stmt = $conn->prepare("UPDATE settings SET value = ? WHERE name = 'maintenance_message'");
        $stmt->execute([$_POST['maintenance_message']]);
    }
    if (isset($_POST['maintenance_email'])) {
        $stmt = $conn->prepare("UPDATE settings SET value = ? WHERE name = 'maintenance_email'");
        $stmt->execute([$_POST['maintenance_email']]);
    }

    $_SESSION['flash_message'] = "✅ Maintenance settings updated.";
    header("Location: index.php?route=dev_dashboard");
    exit;
}

$stmt = $conn->prepare("SELECT value FROM settings WHERE name = 'maintenance_mode'");
$stmt->execute();
$currentMode = $stmt->fetchColumn() ?: 'off';

$stmt = $conn->prepare("SELECT value FROM settings WHERE name = 'maintenance_message'");
$stmt->execute();
$currentMessage = $stmt->fetchColumn() ?: "We’re currently performing updates to improve the system.";

$stmt = $conn->prepare("SELECT value FROM settings WHERE name = 'maintenance_email'");
$stmt->execute();
$currentEmail = $stmt->fetchColumn() ?: "support@gmail.com";

$stmt = $conn->query("SELECT * FROM login_attempts ORDER BY attempt_time DESC LIMIT 100");
$attempts = $stmt->fetchAll(PDO::FETCH_ASSOC);

$failedCounts = [];
foreach ($attempts as $a) {
    $date = date("Y-m-d", strtotime($a['attempt_time']));
    if (!$a['success']) {
        if (!isset($failedCounts[$date])) {
            $failedCounts[$date] = 0;
        }
        $failedCounts[$date]++;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dev Security Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background:#f4f6f9; }
        h1 { color:#2196f3; }
        table { border-collapse: collapse; width: 100%; margin-top: 20px; background:#fff; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background: #eee; }
        .chart-container {
            width: 600px;
            height: 250px;
            margin: 20px auto;
            background:#fff;
            padding:10px;
            border:1px solid #ddd;
            border-radius:5px;
        }
        .maintenance-toggle {
            margin: 20px 0;
            padding: 15px;
            background:#fff;
            border:1px solid #ddd;
            border-radius:5px;
        }
        .logout-btn {
            padding:8px 16px;
            background:#f44336;
            color:#fff;
            border:none;
            border-radius:4px;
            cursor:pointer;
        }
        .logout-btn:hover {
            background:#d32f2f;
        }
    </style>
</head>
<body>
    <h1>Security Monitoring Dashboard (Dev Only)</h1>

    <form action="index.php?route=logout" method="post" style="margin-bottom:20px;">
        <button type="submit" class="logout-btn">Logout</button>
    </form>

    <div class="maintenance-toggle">
        <h2>Maintenance Mode</h2>
        <form method="post">
            <label for="maintenance_mode">Current Mode:</label>
            <select name="maintenance_mode" id="maintenance_mode">
                <option value="off" <?= ($currentMode === 'off') ? 'selected' : '' ?>>Off</option>
                <option value="on" <?= ($currentMode === 'on') ? 'selected' : '' ?>>On</option>
            </select><br><br>

            <label for="maintenance_message">Message:</label><br>
            <textarea name="maintenance_message" id="maintenance_message" rows="3" cols="50"><?= htmlspecialchars($currentMessage) ?></textarea><br><br>

            <label for="maintenance_email">Urgent Contact Email:</label><br>
            <input type="email" name="maintenance_email" id="maintenance_email" value="<?= htmlspecialchars($currentEmail) ?>"><br><br>

            <button type="submit">Update</button>
        </form>
        <p>Status: <strong><?= strtoupper($currentMode) ?></strong></p>
    </div>

    <div class="chart-container">
        <canvas id="failedChart"></canvas>
    </div>
    <script>
        const ctx = document.getElementById('failedChart').getContext('2d');
        const failedChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?= json_encode(array_keys($failedCounts)) ?>,
                datasets: [{
                    label: 'Failed Login Attempts',
                    data: <?= json_encode(array_values($failedCounts)) ?>,
                    backgroundColor: 'rgba(255, 99, 132, 0.7)'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    </script>

    <h2>Recent Login Attempts</h2>
    <table>
        <tr>
            <th>Email/Username</th>
            <th>IP Address</th>
            <th>Success</th>
            <th>Time</th>
        </tr>
        <?php foreach ($attempts as $a): ?>
        <tr>
            <td><?= htmlspecialchars($a['username_or_email']) ?></td>
            <td><?= htmlspecialchars($a['ip_address']) ?></td>
            <td><?= $a['success'] ? '✅ Success' : '❌ Failed' ?></td>
            <td><?= $a['attempt_time'] ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>