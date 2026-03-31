<?php
// security.php — include this at the top of every page

// --- Application Logger ---
function app_log($message) {
    $logFile = dirname(__DIR__) . '/storage/logs/app.log'; // adjust path if needed
    $date = date('Y-m-d H:i:s');
    $entry = "[$date] $message" . PHP_EOL;
    file_put_contents($logFile, $entry, FILE_APPEND);
}

// --- Session Hardening Settings ---
// Only set ini values before session_start, guard against double starts
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);   // prevent JS access to cookies
    ini_set('session.use_strict_mode', 1);   // reject uninitialized session IDs
    ini_set('session.cookie_lifetime', 0);   // expire on browser close
    ini_set('session.use_only_cookies', 1);  // no URL-based sessions

    // Only set secure flag if HTTPS is enabled
    if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
        ini_set('session.cookie_secure', 1);
    }

    session_start();
}

// Regenerate session ID on login or privilege change
function secure_session_regenerate() {
    if (session_status() === PHP_SESSION_ACTIVE) {
        session_regenerate_id(true);
    }
}

// --- CSRF Protection ---
function generate_csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf_token($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}
function validate_csrf_token($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}


// --- Security Headers ---
header("X-Frame-Options: SAMEORIGIN");          // prevent clickjacking
header("X-Content-Type-Options: nosniff");      // block MIME sniffing
header("X-XSS-Protection: 1; mode=block");      // basic XSS filter
header("Referrer-Policy: no-referrer-when-downgrade");
header("Permissions-Policy: geolocation=(), microphone=(), camera=()");

// --- Error Handling ---
ini_set('display_errors', 0);                   // hide errors from users
ini_set('log_errors', 1);
ini_set('error_log', dirname(__DIR__) . '/storage/logs/php_errors.log');

// --- Optional: Auto-inject CSRF into all forms ---
// This ensures every <form> gets a hidden CSRF token automatically
ob_start(function ($buffer) {
    $token = generate_csrf_token();
    $buffer = preg_replace(
        '/(<form[^>]*>)/i',
        '$1<input type="hidden" name="csrf_token" value="' . $token . '">',
        $buffer
    );
    return $buffer;
});