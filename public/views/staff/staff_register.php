<?php
require dirname(__DIR__, 3) . '/app/bootstrap.php';
require dirname(__DIR__, 3) . '/app/helpers/AccessHelper.php';
require dirname(__DIR__, 3) . '/app/security.php';

AccessHelper::requireRole('admin');
?>

<!DOCTYPE html>
<html>
<head>
  <title>Register Staff</title>
</head>
<body>
  <h2>Register Staff Account</h2>
  <form method="post" action="?route=staff_register_action">
    <label>Full Name:</label>
    <input type="text" name="full_name" required>

    <label>Email Address:</label>
    <input type="email" name="email" required>

    <label>Password:</label>
    <input type="password" name="password" required>

    <input type="hidden" name="role" value="staff">

    <button type="submit">Register Staff</button>
  </form>
</body>
</html>