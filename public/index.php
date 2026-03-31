<?php
session_start();
require __DIR__ . '/../app/bootstrap.php';

$conn = db();

$stmt = $conn->prepare("SELECT value FROM settings WHERE name = 'maintenance_mode'");
$stmt->execute();
$mode = $stmt->fetchColumn() ?: 'off';

$route = $_GET['route'] ?? 'home';

if ($mode === 'on' && (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['admin','dev']))) {
    if ($route === 'home') {
        include __DIR__ . '/maintenance.php';
        exit;
    }
}

switch ($route) {
    case 'home':
        include __DIR__ . '/views/home.php';
        break;

    case 'login':
        include __DIR__ . '/views/auth/login.php';
        break;

    case 'register':
        include __DIR__ . '/views/auth/register.php';
        break;

    case 'login_action':
        include __DIR__ . '/login_action.php';
        break;

    case 'register_action':
        include __DIR__ . '/register_action.php';
        break;

    case 'forgot_password':
        include __DIR__ . '/views/auth/forgot_password.php';
        break;

    case 'forgot_password_action':
        include __DIR__ . '/forgot_password_action.php';
        break;

    case 'reset_password':
        include __DIR__ . '/views/auth/reset_password.php';
        break;

    case 'reset_password_action':
        include __DIR__ . '/views/auth/reset_password_action.php';
        break;

    case 'report':
        include __DIR__ . '/views/residents/report.php';
        break;

    case 'report_action':
        include __DIR__ . '/views/residents/report_action.php';
        break;

    case 'resend_alert':
        require __DIR__ . '/../action/resend_alert_action.php';
        break;

    case 'my_reports':
        include __DIR__ . '/views/residents/my_reports.php';
        break;

    case 'edit_report':
        include __DIR__ . '/views/residents/edit_report.php';
        break;

    case 'feedback':
        include __DIR__ . '/views/residents/feedback.php';
        break;

    case 'staff_dashboard':
        include __DIR__ . '/views/staff/dashboard.php';
        break;

    case 'staff_register':
        require __DIR__ . '/views/staff/staff_register.php';
        break;

    case 'staff_register_action':
        require __DIR__ . '/views/staff/staff_register_action.php';
        break;

    case 'update_status':
        require __DIR__ . '/views/staff/update_status.php';
        break;

    case 'admin_dashboard':
        include __DIR__ . '/views/admin/dashboard.php';
        break;

    case 'manage_users':
        include __DIR__ . '/views/admin/manage_users.php';
        break;

    case 'analytics':
        include __DIR__ . '/views/admin/analytics.php';
        break;

    case 'alerts':
        include __DIR__ . '/views/admin/alerts.php';
        break;

    case 'contractors':
        include __DIR__ . '/views/admin/contractors.php';
        break;

    case 'contractor_portal':
        include __DIR__ . '/views/admin/contractors.php';
        break;

    case 'update_user':
        include __DIR__ . '/../app/controllers/update_user.php';
        break;

    case 'add_user':
        include __DIR__ . '/views/admin/add_user.php';
        break;

    case 'save_user':
        include __DIR__ . '/../app/controllers/save_user.php';
        break;

    case 'delete_case':
        include __DIR__ . '/views/admin/delete_case.php';
        break;

    case 'assign_case':
        require __DIR__ . '/views/admin/assign.php';
        break;

    case 'dev_dashboard':
        include __DIR__ . '/views/dev/dashboard.php';
        break;

     case 'logout':
         session_unset();
         session_destroy();
         setcookie("remember_token", "", time() - 3600, "/");
         $_SESSION = []; 
         header("Location: index.php?route=login&success=logged_out");
        exit;

    default:
        echo "<h2>404 Not Found</h2>";
        break;
}