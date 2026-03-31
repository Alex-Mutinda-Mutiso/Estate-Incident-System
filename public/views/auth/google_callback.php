<?php
require __DIR__ . '/../../../vendor/autoload.php';
session_start();

$client = new Google_Client();
$client->setClientId('514257546737-e588brl16f35sfmlrqj6oq2odur3p8cr.apps.googleusercontent.com');
$client->setClientSecret('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');
$client->setRedirectUri('http://localhost/estate_incident_system/public/views/auth/google_callback.php');
$client->addScope('email');
$client->addScope('profile');

if (isset($_GET['code'])) {
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    $client->setAccessToken($token);

    $oauth = new Google_Service_Oauth2($client);
    $userInfo = $oauth->userinfo->get();

    $googleId = $userInfo->id;
    $name     = $userInfo->name;
    $email    = $userInfo->email;
    $picture  = $userInfo->picture;

    require dirname(__DIR__, 3) . '/app/bootstrap.php';
    $conn = db();

    $stmt = $conn->prepare("SELECT * FROM users WHERE email=?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['name']    = $user['name'];
        $_SESSION['email']   = $user['email'];
        $_SESSION['role']    = $user['role'];
    } else {
        $stmt = $conn->prepare("INSERT INTO users (name, email, google_id, role, created_at) VALUES (?, ?, ?, 'resident', NOW())");
        $stmt->execute([$name, $email, $googleId]);

        $_SESSION['user_id'] = $conn->lastInsertId();
        $_SESSION['name']    = $name;
        $_SESSION['email']   = $email;
        $_SESSION['role']    = 'resident';
    }

    switch ($_SESSION['role']) {
        case 'staff':
            header("Location: ../../index.php?route=staff_dashboard");
            break;
        case 'admin':
            header("Location: ../../index.php?route=admin_dashboard");
            break;
        case 'dev':
            header("Location: ../../index.php?route=dev_dashboard");
            break;
        default: // resident
            header("Location: ../../index.php?route=report");
            break;
    }
    exit;
} else {
    echo "Google login failed.";
}