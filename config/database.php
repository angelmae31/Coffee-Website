<?php
// Database configuration
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'cafe_db';

// Create connection without database selection first
$conn = new mysqli($db_host, $db_user, $db_pass);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset to utf8mb4
$conn->set_charset("utf8mb4");

// Try to select the database
if (!$conn->select_db($db_name)) {
    // If database doesn't exist, create it
    $sql = "CREATE DATABASE IF NOT EXISTS $db_name";
    if ($conn->query($sql)) {
        $conn->select_db($db_name);
    } else {
        die("Error creating database: " . $conn->error);
    }
}
?> 