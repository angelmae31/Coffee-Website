<?php
// Include functions
require_once '../includes/functions.php';

// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Set headers for plain text output
header('Content-Type: text/plain');

try {
    // Get database connection
    $conn = getDBConnection();
    
    echo "Connected to database successfully\n\n";
    
    // Check the structure of the order_items table
    $result = $conn->query("DESCRIBE order_items");
    
    if ($result === false) {
        echo "Error retrieving table structure: " . $conn->error . "\n";
    } else {
        echo "Structure of order_items table:\n";
        echo "---------------------------------\n";
        
        while ($row = $result->fetch_assoc()) {
            echo "Field: " . $row['Field'] . "\n";
            echo "Type: " . $row['Type'] . "\n";
            echo "Null: " . $row['Null'] . "\n";
            echo "Key: " . $row['Key'] . "\n";
            echo "Default: " . ($row['Default'] === null ? 'NULL' : $row['Default']) . "\n";
            echo "Extra: " . $row['Extra'] . "\n";
            echo "---------------------------------\n";
        }
    }
    
    // Check existing orders
    $result = $conn->query("SELECT * FROM order_items LIMIT 5");
    
    if ($result === false) {
        echo "Error retrieving order items: " . $conn->error . "\n";
    } else {
        if ($result->num_rows > 0) {
            echo "\nSample data from order_items table:\n";
            echo "---------------------------------\n";
            
            while ($row = $result->fetch_assoc()) {
                foreach ($row as $key => $value) {
                    echo "$key: $value\n";
                }
                echo "---------------------------------\n";
            }
        } else {
            echo "\nNo data found in order_items table\n";
        }
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
} 