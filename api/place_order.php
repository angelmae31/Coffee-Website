<?php
// Include functions
require_once '../includes/functions.php';

// Set headers for JSON response
header('Content-Type: application/json');

// Get database connection
$conn = getDBConnection();

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Get raw POST data
$json = file_get_contents('php://input');
$data = json_decode($json, true);

// Debug: Log the input data
error_log('Order data received: ' . print_r($data, true));

// Check if data is valid
if (!$data || !isset($data['customer']) || !isset($data['items']) || empty($data['items'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid order data'
    ]);
    exit;
}

// Begin transaction
$conn->begin_transaction();

try {
    // Customer information
    $customerName = sanitizeInput($data['customer']['name']);
    $customerEmail = sanitizeInput($data['customer']['email']);
    $customerPhone = sanitizeInput($data['customer']['phone']);
    $specialInstructions = sanitizeInput($data['customer']['specialInstructions'] ?? '');
    
    // Check if customer exists (by email)
    $customerId = null;
    if (!empty($customerEmail)) {
        $stmt = $conn->prepare("SELECT id FROM customers WHERE email = ?");
        $stmt->bind_param("s", $customerEmail);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            // Customer exists
            $customer = $result->fetch_assoc();
            $customerId = $customer['id'];
            
            // Update customer info if name or phone changed
            $stmt = $conn->prepare("UPDATE customers SET name = ?, phone = ? WHERE id = ?");
            $stmt->bind_param("ssi", $customerName, $customerPhone, $customerId);
            $stmt->execute();
        } else {
            // Create new customer
            $stmt = $conn->prepare("INSERT INTO customers (name, email, phone) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $customerName, $customerEmail, $customerPhone);
            $stmt->execute();
            $customerId = $conn->insert_id;
        }
    }
    
    // Calculate total amount
    $totalAmount = 0;
    foreach ($data['items'] as $item) {
        $totalAmount += ($item['price'] * $item['quantity']);
    }
    
    // Generate unique order number
    $orderNumber = 'ORD' . date('YmdHis') . rand(100, 999);
    
    // Create order
    $sql = "INSERT INTO orders (order_number, customer_id, total_amount, special_instructions, status, order_date) VALUES (?, ?, ?, ?, 'pending', NOW())";
    // Debug: Log the SQL
    error_log('SQL for order: ' . $sql);
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sids", $orderNumber, $customerId, $totalAmount, $specialInstructions);
    $stmt->execute();
    $orderId = $conn->insert_id;
    
    // Add order items - restructured to match database schema
    $sql = "INSERT INTO order_items (order_id, menu_item_id, quantity, subtotal) VALUES (?, ?, ?, ?)";
    // Debug: Log the SQL
    error_log('SQL for order items: ' . $sql);
    
    $stmt = $conn->prepare($sql);
    
    foreach ($data['items'] as $item) {
        $menuItemId = $item['id'];
        $quantity = $item['quantity'];
        
        // Calculate subtotal from price and quantity
        $price = isset($item['item_price']) ? $item['item_price'] : (isset($item['price']) ? $item['price'] : 0);
        $subtotal = $price * $quantity;
        
        // Debug: Log item details
        error_log("Processing item: ID=$menuItemId, Quantity=$quantity, Price=$price, Subtotal=$subtotal");
        
        $stmt->bind_param("iiid", $orderId, $menuItemId, $quantity, $subtotal);
        $stmt->execute();
    }
    
    // Commit transaction
    $conn->commit();
    
    // Return success
    echo json_encode([
        'success' => true,
        'message' => 'Order placed successfully',
        'order_id' => $orderId,
        'order_number' => $orderNumber
    ]);
    
} catch (Exception $e) {
    // Rollback transaction on error
    $conn->rollback();
    
    // Debug: Log the detailed error message
    error_log('Error in place_order.php: ' . $e->getMessage() . ' - ' . $e->getTraceAsString());
    
    echo json_encode([
        'success' => false,
        'message' => 'Error placing order: ' . $e->getMessage()
    ]);
} 