<?php
require dirname(__DIR__, 3) . '/app/bootstrap.php';
require dirname(__DIR__, 3) . '/app/helpers/AccessHelper.php';
require dirname(__DIR__, 3) . '/app/security.php';

AccessHelper::requireRole('resident');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF check
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        die("Invalid CSRF token");
    }

    $resident_id  = $_SESSION['user_id'];
    $category     = htmlspecialchars($_POST['category']);
    $description  = htmlspecialchars($_POST['description']);
    $location     = htmlspecialchars($_POST['location']);
    $incidentDate = $_POST['incident_date'] ?? date('Y-m-d');  

    $imageFile = null;
    if (!empty($_FILES['image']['name'])) {
        $uploadDir = dirname(__DIR__, 3) . '/storage/uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','gif'];
        if (in_array($ext, $allowed)) {
            $newName = bin2hex(random_bytes(8)) . '.' . $ext;
            $targetFile = $uploadDir . $newName;

            if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
                $imageFile = $newName;
            }
        }
    }

    $stmt = db()->prepare("
        INSERT INTO complaints 
        (user_id, category, description, location, incident_date, image, status, created_at) 
        VALUES (?, ?, ?, ?, ?, ?, 'pending', NOW())
    ");

    $success = $stmt->execute([
        $resident_id,
        $category,
        $description,
        $location,
        $incidentDate,
        $imageFile
    ]);

    if ($success) {
    
        app_log("Complaint submitted by user {$resident_id}, category: {$category}, image: {$imageFile}");

        $_SESSION['flash_message'] = "Report submitted successfully!";
        header("Location: index.php?route=my_reports&success=report_submitted");
        exit;
    } else {
        echo "Error submitting complaint.";
    }
}
?>