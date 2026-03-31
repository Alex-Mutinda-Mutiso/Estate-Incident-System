<?php
function redirect(string $path) {
  header("Location: {$path}");
  exit;
}
function flash(string $type, string $msg) {
  $_SESSION['flash'][$type] = $msg;
}
function get_flash() {
  $f = $_SESSION['flash'] ?? [];
  $_SESSION['flash'] = [];
  return $f;
}