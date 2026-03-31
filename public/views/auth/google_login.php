<?php
require __DIR__ . '/../../../vendor/autoload.php';
session_start();
$client = new Google_Client();
$client->setClientId('514257546737-e588brl16f35sfmlrqj6oq2odur3p8cr.apps.googleusercontent.com');
$client->setClientSecret('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');
$client->setRedirectUri('http://localhost/estate_incident_system/public/views/auth/google_callback.php');
$client->addScope('email');
$client->addScope('profile');

// Redirect user to Google login
header('Location: ' . $client->createAuthUrl());
exit;