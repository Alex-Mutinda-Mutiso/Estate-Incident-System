<?php
require __DIR__ . '/../app/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $estate_unit = $_POST['estate_unit'];
    $house_number = $_POST['house_number'];
    $estate_number = $_POST['estate_number'];

    try {
        $conn = db(); 

        $stmt = $conn->prepare("INSERT INTO users (name, email, password_hash, estate_unit, house_number, estate_number) VALUES (?, ?, ?, ?, ?, ?)");
        $success = $stmt->execute([$name, $email, $password, $estate_unit, $house_number, $estate_number]);

        if ($success) {
            header("Location: index.php?route=login&success=registered");
            exit;
        } else {
            $errorInfo = $stmt->errorInfo();
            echo "Error: " . $errorInfo[2];
        }
    } catch (PDOException $e) {
        echo "Database Error: " . $e->getMessage();
    }
}
?>