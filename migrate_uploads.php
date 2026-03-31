<?php
require __DIR__ . '/app/bootstrap.php';

// Paths
$oldDir = __DIR__ . '/uploads/';
$newDir = __DIR__ . '/storage/uploads/';

// Ensure new directory exists
if (!is_dir($newDir)) {
    mkdir($newDir, 0777, true);
}

// Scan old uploads
$files = glob($oldDir . '*.*');

foreach ($files as $filePath) {
    $filename = basename($filePath);
    $newPath = $newDir . $filename;

    // Move file
    if (rename($filePath, $newPath)) {
        echo "Moved: $filename\n";

        // Update database records
        $stmt = db()->prepare("UPDATE complaints SET image = ? WHERE image = ?");
        $stmt->execute([$filename, $filename]);
    } else {
        echo "Failed to move: $filename\n";
    }
}

echo "Migration complete.\n";