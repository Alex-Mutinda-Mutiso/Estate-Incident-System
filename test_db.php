<?php
require __DIR__ . '/app/db.php';

try {
    $pdo = db();
    echo "✅ Database connection successful!";
} catch (Exception $e) {
    echo "❌ Connection failed: " . $e->getMessage();
}
