<?php
require dirname(__DIR__, 3) . '/app/bootstrap.php';
require dirname(__DIR__, 3) . '/app/security.php';
AccessHelper::requireRole('admin');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $complaintId = $_POST['id'] ?? null;
    $action      = $_POST['action'] ?? null;

    if ($action === 'assign' && $complaintId && isset($_POST['assignee'])) {
        // Split "id|type" value
        list($assigneeId, $type) = explode('|', $_POST['assignee']);

        if ($type === 'contractor') {
            $stmt = db()->prepare("UPDATE complaints 
                                   SET contractor_id = ?, assigned_to = NULL, status = 'Assigned' 
                                   WHERE id = ?");
            $stmt->execute([$assigneeId, $complaintId]);

            $_SESSION['flash_message'] = "Complaint #{$complaintId} assigned to contractor #{$assigneeId}.";
        } elseif ($type === 'staff') {
            $stmt = db()->prepare("UPDATE complaints 
                                   SET assigned_to = ?, contractor_id = NULL, status = 'Assigned' 
                                   WHERE id = ?");
            $stmt->execute([$assigneeId, $complaintId]);

            $_SESSION['flash_message'] = "Complaint #{$complaintId} assigned to staff #{$assigneeId}.";
        } else {
            $_SESSION['flash_message'] = "Invalid assignment target.";
        }

        header("Location: index.php?route=contractors&success=assigned");
        exit;
    }

    if ($action === 'update_status' && $complaintId && isset($_POST['status'])) {
        $status = $_POST['status'];
        $stmt = db()->prepare("UPDATE complaints SET status = ? WHERE id = ?");
        $stmt->execute([$status, $complaintId]);

        $_SESSION['flash_message'] = "Complaint #{$complaintId} status updated to {$status}.";
        header("Location: index.php?route=contractors&success=status_updated");
        exit;
    }

    $_SESSION['flash_message'] = "Invalid action or missing data.";
    header("Location: index.php?route=contractors&error=action_failed");
    exit;
}