<?php
require __DIR__ . '/../app/bootstrap.php';
require __DIR__ . '/../app/helpers/AccessHelper.php';

AccessHelper::requireRole('admin');

$id = $_POST['id'];
$role = $_POST['role'];

$stmt = db()->prepare("UPDATE users SET role=? WHERE id=?");
$stmt->execute([$role,$id]);

flash('success','User role updated.');
redirect("?route=admin_dashboard");