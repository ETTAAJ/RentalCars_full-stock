<?php
require_once 'config.php';

// Hardcoded credentials
$username = 'admin';
$password = 'admin123';  // This will be your login password

// Generate correct hash
$hash = password_hash($password, PASSWORD_DEFAULT);

// Update or insert admin
try {
    $stmt = $pdo->prepare("
        INSERT INTO admins (username, password) 
        VALUES (?, ?) 
        ON DUPLICATE KEY UPDATE password = ?
    ");
    $stmt->execute([$username, $hash, $hash]);

    echo "<h3 style='color:green'>Admin Password Reset Successfully!</h3>";
    echo "<p><strong>Username:</strong> admin</p>";
    echo "<p><strong>Password:</strong> admin123</p>";
    echo "<p><a href='login.php' style='color:blue; text-decoration:underline;'>Go to Login</a></p>";
} catch (Exception $e) {
    echo "<p style='color:red'>Error: " . $e->getMessage() . "</p>";
}
?>