<?php
// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Include functions
require_once '../includes/functions.php';

// Check if user is logged in
if (!isLoggedIn()) {
    redirect('index.php');
}

// Get current page
$currentPage = basename($_SERVER['PHP_SELF']);

// Get database connection
$conn = getDBConnection();

// Check if order ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    redirect('orders.php');
}

$orderId = (int)$_GET['id'];

// Get order details
$sql = "SELECT o.*, c.name as customer_name, c.email, c.phone
        FROM orders o 
        LEFT JOIN customers c ON o.customer_id = c.id 
        WHERE o.id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $orderId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    redirect('orders.php');
}

$order = $result->fetch_assoc();

// Get order items
$sql = "SELECT oi.*, m.name, m.image, m.price as menu_price
        FROM order_items oi 
        LEFT JOIN menu_items m ON oi.menu_item_id = m.id 
        WHERE oi.order_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $orderId);
$stmt->execute();
$result = $stmt->get_result();

$orderItems = [];
while ($row = $result->fetch_assoc()) {
    // Add a calculated price if it doesn't exist
    if (!isset($row['price'])) {
        $row['price'] = isset($row['menu_price']) ? $row['menu_price'] : 
                        ($row['quantity'] > 0 ? $row['subtotal'] / $row['quantity'] : 0);
    }
    $orderItems[] = $row;
}

// Handle status update
if (isset($_POST['update_status']) && !empty($_POST['status'])) {
    $status = sanitizeInput($_POST['status']);
    
    // Validate status
    $validStatuses = ['pending', 'processing', 'completed', 'cancelled'];
    if (in_array($status, $validStatuses)) {
        $sql = "UPDATE orders SET status = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $status, $orderId);
        
        if ($stmt->execute()) {
            $successMsg = "Order status updated to " . ucfirst($status);
            // Update the order array to reflect the change
            $order['status'] = $status;
        } else {
            $errorMsg = "Error updating order status: " . $conn->error;
        }
    } else {
        $errorMsg = "Invalid status";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Green Cafe</title>
    
    <!-- Favicon -->
    <link rel="shortcut icon" href="../assets/images/favicon.ico" type="image/x-icon">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="admin-container">
        <aside class="sidebar">
            <div class="admin-logo">
                <h2 class="admin-title">Green Cafe</h2>
            </div>
            
            <ul class="admin-nav">
                <li class="admin-nav-item">
                    <a href="dashboard.php" class="admin-nav-link <?php echo $currentPage === 'dashboard.php' ? 'active' : ''; ?>">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                </li>
                <li class="admin-nav-item">
                    <a href="orders.php" class="admin-nav-link <?php echo $currentPage === 'orders.php' || $currentPage === 'view_order.php' ? 'active' : ''; ?>">
                        <i class="fas fa-shopping-cart"></i> Orders
                    </a>
                </li>
                <li class="admin-nav-item">
                    <a href="menu.php" class="admin-nav-link <?php echo $currentPage === 'menu.php' || $currentPage === 'add_menu_item.php' || $currentPage === 'edit_menu_item.php' ? 'active' : ''; ?>">
                        <i class="fas fa-utensils"></i> Menu Items
                    </a>
                </li>
                <li class="admin-nav-item">
                    <a href="messages.php" class="admin-nav-link <?php echo $currentPage === 'messages.php' || $currentPage === 'view_message.php' ? 'active' : ''; ?>">
                        <i class="fas fa-envelope"></i> Messages
                    </a>
                </li>
                <li class="admin-nav-item">
                    <a href="logout.php" class="admin-nav-link">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </li>
            </ul>
        </aside>
        
        <div class="admin-content">
            <header class="admin-header">
                <h1 class="admin-heading">
                    <?php 
                    if ($currentPage === 'dashboard.php') echo 'Dashboard';
                    elseif ($currentPage === 'orders.php') echo 'Manage Orders';
                    elseif ($currentPage === 'view_order.php') echo 'Order Details';
                    elseif ($currentPage === 'menu.php') echo 'Manage Menu';
                    elseif ($currentPage === 'add_menu_item.php') echo 'Add Menu Item';
                    elseif ($currentPage === 'edit_menu_item.php') echo 'Edit Menu Item';
                    elseif ($currentPage === 'messages.php') echo 'Customer Messages';
                    elseif ($currentPage === 'view_message.php') echo 'Message Details';
                    else echo 'Admin Panel';
                    ?>
                </h1>
                <div>
                    <span>Welcome, <?php echo $_SESSION['username']; ?></span>
                </div>
            </header>
            
            <div class="admin-content-inner" style="padding: 20px;">
                <!-- Success/Error Messages -->
                <?php if (isset($successMsg)): ?>
                    <div style="background-color: #d4edda; color: #155724; padding: 15px; border-radius: 4px; margin-bottom: 20px;">
                        <?php echo $successMsg; ?>
                    </div>
                <?php endif; ?>

                <?php if (isset($errorMsg)): ?>
                    <div style="background-color: #f8d7da; color: #721c24; padding: 15px; border-radius: 4px; margin-bottom: 20px;">
                        <?php echo $errorMsg; ?>
                    </div>
                <?php endif; ?>

                <!-- Order Details -->
                <div class="admin-card">
                    <div class="admin-card-header">
                        <h2 class="admin-card-title">Order #<?php echo $order['order_number']; ?></h2>
                        <a href="orders.php" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Orders
                        </a>
                    </div>
                    
                    <div style="display: flex; flex-wrap: wrap; gap: 20px; margin-bottom: 30px;">
                        <div style="flex: 1; min-width: 250px;">
                            <h3 style="font-size: 1.2rem; color: var(--green); margin-bottom: 15px;">Order Information</h3>
                            
                            <table style="width: 100%;">
                                <tr>
                                    <td style="padding: 8px 0; border-bottom: 1px solid var(--medium-gray); font-weight: bold;">Order Number:</td>
                                    <td style="padding: 8px 0; border-bottom: 1px solid var(--medium-gray);"><?php echo $order['order_number']; ?></td>
                                </tr>
                                <tr>
                                    <td style="padding: 8px 0; border-bottom: 1px solid var(--medium-gray); font-weight: bold;">Date:</td>
                                    <td style="padding: 8px 0; border-bottom: 1px solid var(--medium-gray);"><?php echo date('F d, Y H:i', strtotime($order['order_date'])); ?></td>
                                </tr>
                                <tr>
                                    <td style="padding: 8px 0; border-bottom: 1px solid var(--medium-gray); font-weight: bold;">Status:</td>
                                    <td style="padding: 8px 0; border-bottom: 1px solid var(--medium-gray);">
                                        <span class="status-badge status-<?php echo $order['status']; ?>">
                                            <?php echo ucfirst($order['status']); ?>
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 8px 0; border-bottom: 1px solid var(--medium-gray); font-weight: bold;">Total Amount:</td>
                                    <td style="padding: 8px 0; border-bottom: 1px solid var(--medium-gray); font-weight: bold; color: var(--green);">
                                        <?php echo formatPrice($order['total_amount']); ?>
                                    </td>
                                </tr>
                            </table>
                            
                            <!-- Status Update Form -->
                            <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'] . '?id=' . $orderId); ?>" style="margin-top: 20px;">
                                <div style="display: flex; gap: 10px;">
                                    <select name="status" class="form-control" style="flex: 1;">
                                        <option value="">Update Status</option>
                                        <option value="pending" <?php echo $order['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                        <option value="processing" <?php echo $order['status'] === 'processing' ? 'selected' : ''; ?>>Processing</option>
                                        <option value="completed" <?php echo $order['status'] === 'completed' ? 'selected' : ''; ?>>Completed</option>
                                        <option value="cancelled" <?php echo $order['status'] === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                    </select>
                                    <button type="submit" name="update_status" class="btn btn-primary">Update</button>
                                </div>
                            </form>
                        </div>
                        
                        <div style="flex: 1; min-width: 250px;">
                            <h3 style="font-size: 1.2rem; color: var(--green); margin-bottom: 15px;">Customer Information</h3>
                            
                            <?php if (!empty($order['customer_name'])): ?>
                                <div class="customer-card" style="background-color: #f8f9fa; border-radius: 8px; padding: 15px; margin-bottom: 20px;">
                                    <div style="display: flex; align-items: center; margin-bottom: 15px;">
                                        <div style="background-color: #e8f5e9; width: 40px; height: 40px; border-radius: 50%; display: flex; justify-content: center; align-items: center; margin-right: 12px;">
                                            <i class="fas fa-user" style="color: var(--green);"></i>
                                        </div>
                                        <div>
                                            <div style="font-weight: 600; font-size: 1.1rem;"><?php echo htmlspecialchars($order['customer_name']); ?></div>
                                            <?php if (!empty($order['customer_id'])): ?>
                                                <div style="color: #6c757d; font-size: 0.85rem;">Customer ID: #<?php echo $order['customer_id']; ?></div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    
                                    <div style="border-top: 1px solid #dee2e6; padding-top: 12px;">
                                        <?php if (!empty($order['email'])): ?>
                                            <div style="display: flex; align-items: center; margin-bottom: 8px;">
                                                <i class="fas fa-envelope" style="width: 20px; color: var(--green); margin-right: 10px;"></i>
                                                <a href="mailto:<?php echo htmlspecialchars($order['email']); ?>" style="color: #0d6efd; text-decoration: none;">
                                                    <?php echo htmlspecialchars($order['email']); ?>
                                                </a>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <?php if (!empty($order['phone'])): ?>
                                            <div style="display: flex; align-items: center; margin-bottom: 8px;">
                                                <i class="fas fa-phone" style="width: 20px; color: var(--green); margin-right: 10px;"></i>
                                                <a href="tel:<?php echo htmlspecialchars($order['phone']); ?>" style="color: #0d6efd; text-decoration: none;">
                                                    <?php echo htmlspecialchars($order['phone']); ?>
                                                </a>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php else: ?>
                                <div class="customer-card" style="background-color: #f8f9fa; border-radius: 8px; padding: 15px; margin-bottom: 20px;">
                                    <div style="display: flex; align-items: center;">
                                        <div style="background-color: #f0f0f0; width: 40px; height: 40px; border-radius: 50%; display: flex; justify-content: center; align-items: center; margin-right: 12px;">
                                            <i class="fas fa-user" style="color: #aaa;"></i>
                                        </div>
                                        <div>
                                            <div style="font-weight: 600; font-size: 1.1rem;">Guest Order</div>
                                            <div style="color: #6c757d; font-size: 0.85rem;">No customer account associated</div>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($order['special_instructions'])): ?>
                                <h3 style="font-size: 1.2rem; color: var(--green); margin: 20px 0 15px;">Special Instructions</h3>
                                <div style="padding: 15px; background-color: #fff3cd; border-left: 4px solid #ffc107; border-radius: 4px; color: #856404;">
                                    <i class="fas fa-info-circle" style="margin-right: 8px;"></i>
                                    <?php echo nl2br(htmlspecialchars($order['special_instructions'])); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <h3 style="font-size: 1.2rem; color: var(--green); margin: 20px 0 15px;">Order Items</h3>
                    
                    <?php if (count($orderItems) > 0): ?>
                        <div class="table-responsive">
                            <table class="admin-table">
                                <thead>
                                    <tr>
                                        <th>Item</th>
                                        <th>Price</th>
                                        <th>Quantity</th>
                                        <th>Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($orderItems as $item): ?>
                                        <tr>
                                            <td>
                                                <div style="display: flex; align-items: center;">
                                                    <?php if (!empty($item['image'])): ?>
                                                        <div style="width: 50px; height: 50px; margin-right: 10px; overflow: hidden; border-radius: 4px;">
                                                            <img src="../assets/images/menu/<?php echo $item['image']; ?>" alt="<?php echo $item['name']; ?>" style="width: 100%; height: 100%; object-fit: cover;">
                                                        </div>
                                                    <?php endif; ?>
                                                    <div>
                                                        <?php echo $item['name']; ?>
                                                        <?php if ($item['menu_item_id'] === null): ?>
                                                            <span style="color: #dc3545; font-size: 0.8rem;">(Item no longer available)</span>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><?php echo isset($item['price']) ? formatPrice($item['price']) : formatPrice(0); ?></td>
                                            <td><?php echo $item['quantity']; ?></td>
                                            <td><?php echo formatPrice($item['subtotal']); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                    <tr>
                                        <td colspan="3" style="text-align: right; font-weight: bold;">Total:</td>
                                        <td style="font-weight: bold; color: var(--green);"><?php echo formatPrice($order['total_amount']); ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p>No items found for this order.</p>
                    <?php endif; ?>
                </div>
                
                <footer style="text-align: center; margin-top: 50px; padding-top: 20px; border-top: 1px solid var(--medium-gray);">
                    <p>&copy; <?php echo date('Y'); ?> Green Cafe Admin Panel. All Rights Reserved.</p>
                </footer>
            </div> <!-- Close admin-content-inner div -->
        </div> <!-- Close admin-content div -->
    </div> <!-- Close admin-container div -->

    <!-- JavaScript -->
    <script src="../assets/js/main.js"></script>
</body>
</html> 