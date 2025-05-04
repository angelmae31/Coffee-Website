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
    
    // Check if table exists
    $result = $conn->query("SHOW TABLES LIKE 'order_items'");
    
    if ($result->num_rows === 0) {
        echo "Creating order_items table...\n";
        
        // Create the table with the correct structure
        $sql = "CREATE TABLE `order_items` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `order_id` int(11) NOT NULL,
            `menu_item_id` int(11) NOT NULL,
            `quantity` int(11) NOT NULL DEFAULT 1,
            `subtotal` decimal(10,2) NOT NULL,
            `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            KEY `order_id` (`order_id`),
            KEY `menu_item_id` (`menu_item_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
        
        if ($conn->query($sql)) {
            echo "Table created successfully\n";
        } else {
            echo "Error creating table: " . $conn->error . "\n";
        }
    } else {
        echo "order_items table already exists\n";
        
        // Check if we need to modify the table structure
        $result = $conn->query("DESCRIBE order_items");
        
        $hasPrice = false;
        while ($row = $result->fetch_assoc()) {
            if ($row['Field'] === 'price') {
                $hasPrice = true;
                break;
            }
        }
        
        if ($hasPrice) {
            echo "Table has 'price' column - removing it to match our new schema...\n";
            
            // Try to remove the price column
            if ($conn->query("ALTER TABLE order_items DROP COLUMN price")) {
                echo "Successfully removed price column\n";
            } else {
                echo "Error removing price column: " . $conn->error . "\n";
            }
        } else {
            echo "Table structure is correct (no price column)\n";
        }
    }
    
    echo "\nDatabase setup complete\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
} 