<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require dirname(__DIR__) . '/app/bootstrap.php';
require dirname(__DIR__) . '/app/security.php';
require dirname(__DIR__) . '/app/helpers/AccessHelper.php';

AccessHelper::requireRole('resident');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require dirname(__DIR__) . '/vendor/autoload.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $complaint_id = $_POST['complaint_id'] ?? null;
    $csrf_token   = $_POST['csrf_token'] ?? '';

    if (!validate_csrf_token($csrf_token)) {
        die("Invalid CSRF token.");
    }

    if (!$complaint_id) {
        die("Complaint ID missing.");
    }

    $conn = db();
    $stmt = $conn->prepare("SELECT * FROM complaints WHERE id = ? AND user_id = ?");
    $stmt->execute([$complaint_id, $_SESSION['user_id']]);
    $complaint = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$complaint) {
        die("Complaint not found.");
    }

    $subject = "Resend Alert: Complaint #{$complaint['id']} ({$complaint['category']})";
    $body = "
        Resident: {$_SESSION['name']}<br>
        Category: {$complaint['category']}<br>
        Description: {$complaint['description']}<br>
        Location: {$complaint['location']}<br>
        Date: {$complaint['incident_date']}<br>
        Status: {$complaint['status']}<br>
        <br>
        This is a resend alert for an unresolved complaint.
    ";

    $mail = new PHPMailer(true);

    try {
        
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'sanchezaleko001@gmail.com';
        $mail->Password   = 'whwp odga lywy sqkv'; 
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->setFrom('sanchezaleko001@gmail.com', 'Estate Incident System');
        $mail->addAddress('sanchezaleko001@gmail.com'); 
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $body;

        $mail->send();

        $logStmt = $conn->prepare(
            "INSERT INTO alerts (message, recipients, created_at) VALUES (?, ?, NOW())"
        );
        $logStmt->execute([
            "Resent alert for complaint {$complaint['id']}",
            "residents" 
        ]);

        $_SESSION['flash_message'] = "Alert resent successfully!";
    } catch (Exception $e) {
        $_SESSION['flash_message'] = "Failed to resend alert. Mailer Error: {$mail->ErrorInfo}";
    }

    header("Location: index.php?route=my_reports");
    exit;
}