<?php
// Set headers for HTML output
header('Content-Type: text/html');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fix Database Error</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        h1 {
            color: #333;
            border-bottom: 2px solid #ddd;
            padding-bottom: 10px;
        }
        h2 {
            color: #444;
            margin-top: 25px;
        }
        pre {
            background-color: #f4f4f4;
            padding: 15px;
            border-radius: 5px;
            overflow-x: auto;
        }
        .btn {
            display: inline-block;
            background-color: #4CAF50;
            color: white;
            padding: 10px 15px;
            text-decoration: none;
            border-radius: 4px;
            cursor: pointer;
            border: none;
            margin: 5px 0;
        }
        .btn:hover {
            background-color: #45a049;
        }
        .error {
            color: #721c24;
            background-color: #f8d7da;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        .success {
            color: #155724;
            background-color: #d4edda;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Fix Database Error</h1>
        
        <div class="error">
            <strong>Error:</strong> Unknown column 'price' in 'field list'
        </div>
        
        <h2>Problem Explanation</h2>
        <p>
            The error occurs because the checkout process is trying to insert data into a 'price' column in the 'order_items' table,
            but this column doesn't exist in your database structure.
        </p>
        
        <h2>Solution Options</h2>
        
        <h3>Option 1: Run Database Fix Script (Recommended)</h3>
        <p>
            Click the button below to run a script that will automatically check your database structure 
            and fix it to match the requirements:
        </p>
        <a href="setup_db.php" class="btn">Run Database Fix Script</a>
        
        <h3>Option 2: Check Database Structure</h3>
        <p>
            To see the current structure of your 'order_items' table and diagnose issues:
        </p>
        <a href="debug_db.php" class="btn">Check Database Structure</a>
        
        <h2>Manual SQL Fix</h2>
        <p>
            If the automatic fix doesn't work, you can run the following SQL in phpMyAdmin or other database management tool:
        </p>
        
        <h3>If the column doesn't exist, add it:</h3>
        <pre>ALTER TABLE `order_items` ADD COLUMN `price` DECIMAL(10,2) NOT NULL AFTER `quantity`;</pre>
        
        <h3>Or, update the place_order.php file SQL statement:</h3>
        <p>
            Alternatively, modify the SQL in place_order.php to only use columns that exist in your database.
            We've attempted to do this automatically in our latest update.
        </p>
        
        <h2>After Fixing</h2>
        <p>
            After applying one of these solutions, try the checkout process again.
            If you still encounter issues, check the server error logs for more details.
        </p>
        
        <a href="../pages/menu.php" class="btn">Return to Menu Page</a>
    </div>
</body>
</html> 