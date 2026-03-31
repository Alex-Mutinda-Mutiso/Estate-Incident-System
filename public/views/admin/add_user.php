<?php
require dirname(__DIR__, 3) . '/app/bootstrap.php';
require dirname(__DIR__, 3) . '/app/security.php';


if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php?route=login");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Add User</title>
  <link rel="stylesheet" href="/estate_incident_system/public/css/styles.css">
</head>
<body>
  <header>
    <h1>Add New User</h1>
  </header>
  <div class="container">
    <form method="post" action="index.php?route=save_user">
      <label>Name:</label><br>
      <input type="text" name="name" required><br><br>

      <label>Email:</label><br>
      <input type="email" name="email" required><br><br>

      <label>Password:</label><br>
      <input type="password" name="password" required><br><br>

      <label>Role:</label><br>
      <select name="role" required>
        <option value="resident">Resident</option>
        <option value="staff">Staff</option>
        <option value="admin">Admin</option>
      </select><br><br>

      <button type="submit">Create User</button>
    </form>
  </div>
</body>
</html>