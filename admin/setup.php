<?php
require_once '../config/database.php';

// First, check if database exists
$db_check = $conn->query("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = 'cafe_db'");

if ($db_check->num_rows == 0) {
    // Create database if it doesn't exist
    if ($conn->query("CREATE DATABASE IF NOT EXISTS cafe_db")) {
        echo "Database created successfully.<br>";
        // Select the database
        $conn->select_db("cafe_db");
    } else {
        die("Error creating database: " . $conn->error);
    }
} else {
    // Select the database
    $conn->select_db("cafe_db");
}

// SQL to create admins table
$sql = "CREATE TABLE IF NOT EXISTS admins (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";

// Execute the query
if ($conn->query($sql) === TRUE) {
    echo "Admins table created successfully!<br>";
    echo "You can now <a href='register.php'>register</a> your admin account.";
} else {
    echo "Error creating table: " . $conn->error;
}

// Verify the table was created
$table_check = $conn->query("SHOW TABLES LIKE 'admins'");
if ($table_check->num_rows > 0) {
    echo "<br>Table verification: Admins table exists and is ready to use.";
} else {
    echo "<br>Warning: Table verification failed. Please check your database permissions.";
}

$conn->close();
?> 