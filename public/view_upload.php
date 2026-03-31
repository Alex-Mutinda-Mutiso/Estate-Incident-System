<?php
require dirname(__DIR__) . '/app/bootstrap.php';
require dirname(__DIR__) . '/app/security.php';
require dirname(__DIR__) . '/app/helpers/AccessHelper.php';

$file = $_GET['file'] ?? '';
$download = isset($_GET['download']);

// ✅ Correct path
$path = dirname(__DIR__) . '/storage/uploads/' . basename($file);

if (file_exists($path)) {
    $mime = mime_content_type($path);
    if ($download) {
        header("Content-Type: application/octet-stream");
        header("Content-Disposition: attachment; filename=\"" . basename($path) . "\"");
    } else {
        header("Content-Type: $mime");
    }
    readfile($path);
    exit;
} else {
    http_response_code(404);
    echo "File not found.";
}