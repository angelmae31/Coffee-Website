<?php
// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include database connection
require_once 'config/db.php';

// Admin credentials
$username = 'admin';
$password = 'admin123';  // This will be the password to login
$role = 'admin';

// Hash the password
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

try {
    // Get database connection
    $conn = getDBConnection();
    
    // Check if user already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        // Update existing admin user
        $stmt = $conn->prepare("UPDATE users SET password = ? WHERE username = ?");
        $stmt->bind_param("ss", $hashed_password, $username);
        if ($stmt->execute()) {
            echo "Admin password updated successfully!<br>";
            echo "Username: " . $username . "<br>";
            echo "Password: " . $password . "<br>";
        } else {
            echo "Error updating admin: " . $conn->error;
        }
    } else {
        // Create new admin user
        $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $hashed_password, $role);
        if ($stmt->execute()) {
            echo "Admin user created successfully!<br>";
            echo "Username: " . $username . "<br>";
            echo "Password: " . $password . "<br>";
        } else {
            echo "Error creating admin: " . $conn->error;
        }
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?> 