<?php
require_once '../config/database.php';

// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h2>Database Cleanup</h2>";

try {
    // Execute the DROP TABLE command
    $sql = "DROP TABLE IF EXISTS users";
    if ($conn->query($sql)) {
        echo "<p style='color: green;'>✓ Users table has been successfully removed.</p>";
    } else {
        echo "<p style='color: red;'>✗ Error removing users table: " . $conn->error . "</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Error: " . $e->getMessage() . "</p>";
}

// Close the connection
$conn->close();

echo "<p><a href='dashboard.php'>Return to Dashboard</a></p>";
?> 