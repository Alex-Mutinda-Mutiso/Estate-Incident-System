<?php
require dirname(__DIR__) . '/app/bootstrap.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');

    try {
        $conn = db();
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            $token   = bin2hex(random_bytes(16));
            $expires = date('Y-m-d H:i:s', time() + 3600); // 1 hour expiry

            try {
                $stmt = $conn->prepare(
                    "INSERT INTO password_resets (user_id, token, expires_at) VALUES (?, ?, ?)"
                );
                $stmt->execute([$user['id'], $token, $expires]);
            } catch (PDOException $e) {
                echo "Insert Error: " . $e->getMessage();
                exit;
            }

            $resetLink = "http://localhost/estate_incident_system/public/index.php?route=reset_password&token=" . urlencode($token);

            
            require dirname(__DIR__) . '/vendor/autoload.php';
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
                $mail->addAddress($email, $user['name']);

                $mail->isHTML(true);
                $mail->Subject = 'Password Reset Request';
                $mail->Body    = "Hello {$user['name']},<br><br>
                                  Click the link below to reset your password:<br>
                                  <a href='$resetLink'>$resetLink</a><br><br>
                                  This link will expire in 1 hour.";

                $mail->send();
                echo "Password reset email sent to $email.";
            } catch (Exception $e) {
                echo "Mailer Error: {$mail->ErrorInfo}";
            }
        } else {
            echo "No account found with that email.";
        }
    } catch (PDOException $e) {
        echo "Database Error: " . $e->getMessage();
    }
}