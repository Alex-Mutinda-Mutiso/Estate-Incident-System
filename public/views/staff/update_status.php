<?php
require dirname(__DIR__, 3) . '/app/bootstrap.php';
require dirname(__DIR__, 3) . '/app/helpers/AccessHelper.php';
require dirname(__DIR__, 3) . '/app/security.php';

AccessHelper::requireRole('staff');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $complaintId = $_POST['id'] ?? null;
    $status      = $_POST['status'] ?? null;
    $remarks     = $_POST['remarks'] ?? null;

    if ($complaintId && $status) {
        $stmt = db()->prepare("UPDATE complaints SET status = ?, remarks = ? WHERE id = ?");
        $stmt->execute([$status, $remarks, $complaintId]);

        $_SESSION['flash_message'] = "Complaint #{$complaintId} updated to '{$status}'" 
            . ($remarks ? " with remarks: {$remarks}" : ".");
        
        header("Location: index.php?route=staff_dashboard&success=status_updated");
        exit;
    } else {
        $_SESSION['flash_message'] = "Invalid request. Please select a status.";
        header("Location: index.php?route=staff_dashboard&error=invalid_request");
        exit;
    }
}