<?php
/**
 * Database Configuration
 * 
 * Contains database connection parameters and establishes the connection.
 */

// Database credentials
define('DB_HOST', 'localhost');
define('DB_USER', 'root'); // Default XAMPP username
define('DB_PASS', '');     // Default XAMPP password (empty)
define('DB_NAME', 'cafe_db');

// Create a database connection
function connectDB() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    // Check the connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    return $conn;
}

// Get database connection
function getDBConnection() {
    static $conn = null;
    
    if ($conn === null) {
        $conn = connectDB();
    }
    
    return $conn;
}

// Close database connection
function closeDBConnection($conn) {
    if ($conn) {
        $conn->close();
    }
} 