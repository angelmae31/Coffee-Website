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

// Get statistics
// Total orders
$result = $conn->query("SELECT COUNT(*) as total FROM orders");
$totalOrders = $result->fetch_assoc()['total'];

// Pending orders
$result = $conn->query("SELECT COUNT(*) as pending FROM orders WHERE status = 'pending'");
$pendingOrders = $result->fetch_assoc()['pending'];

// Total menu items
$result = $conn->query("SELECT COUNT(*) as total FROM menu_items");
$totalMenuItems = $result->fetch_assoc()['total'];

// Total contact messages
$result = $conn->query("SELECT COUNT(*) as total FROM contact_messages");
$totalMessages = $result->fetch_assoc()['total'];

// Unread messages
$result = $conn->query("SELECT COUNT(*) as unread FROM contact_messages WHERE is_read = 0");
$unreadMessages = $result->fetch_assoc()['unread'];

// Get recent orders
$recentOrders = getRecentOrders(5);
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
                <!-- Dashboard Overview -->
                <div class="row" style="display: flex; flex-wrap: wrap; gap: 20px; margin-bottom: 30px;">
                    <div class="admin-card" style="flex: 1; min-width: 200px;">
                        <div style="display: flex; align-items: center;">
                            <div style="background-color: rgba(87, 126, 76, 0.1); padding: 15px; border-radius: 50%; margin-right: 15px;">
                                <i class="fas fa-shopping-cart" style="color: var(--green); font-size: 1.8rem;"></i>
                            </div>
                            <div>
                                <h3 style="font-size: 1.8rem; color: var(--green);"><?php echo $totalOrders; ?></h3>
                                <p>Total Orders</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="admin-card" style="flex: 1; min-width: 200px;">
                        <div style="display: flex; align-items: center;">
                            <div style="background-color: rgba(255, 193, 7, 0.1); padding: 15px; border-radius: 50%; margin-right: 15px;">
                                <i class="fas fa-clock" style="color: #ffc107; font-size: 1.8rem;"></i>
                            </div>
                            <div>
                                <h3 style="font-size: 1.8rem; color: #ffc107;"><?php echo $pendingOrders; ?></h3>
                                <p>Pending Orders</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="admin-card" style="flex: 1; min-width: 200px;">
                        <div style="display: flex; align-items: center;">
                            <div style="background-color: rgba(13, 110, 253, 0.1); padding: 15px; border-radius: 50%; margin-right: 15px;">
                                <i class="fas fa-utensils" style="color: #0d6efd; font-size: 1.8rem;"></i>
                            </div>
                            <div>
                                <h3 style="font-size: 1.8rem; color: #0d6efd;"><?php echo $totalMenuItems; ?></h3>
                                <p>Menu Items</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="admin-card" style="flex: 1; min-width: 200px;">
                        <div style="display: flex; align-items: center;">
                            <div style="background-color: rgba(220, 53, 69, 0.1); padding: 15px; border-radius: 50%; margin-right: 15px;">
                                <i class="fas fa-envelope" style="color: #dc3545; font-size: 1.8rem;"></i>
                            </div>
                            <div>
                                <h3 style="font-size: 1.8rem; color: #dc3545;"><?php echo $unreadMessages; ?></h3>
                                <p>Unread Messages</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Orders -->
                <div class="admin-card">
                    <div class="admin-card-header">
                        <h2 class="admin-card-title">Recent Orders</h2>
                        <a href="orders.php" class="btn btn-primary">View All</a>
                    </div>
                    
                    <?php if (count($recentOrders) > 0): ?>
                        <div class="table-responsive">
                            <table class="admin-table">
                                <thead>
                                    <tr>
                                        <th>Order #</th>
                                        <th>Customer</th>
                                        <th>Date</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recentOrders as $order): ?>
                                        <tr>
                                            <td><?php echo $order['order_number']; ?></td>
                                            <td><?php echo $order['customer_name'] ?? 'Guest'; ?></td>
                                            <td><?php echo date('M d, Y', strtotime($order['order_date'])); ?></td>
                                            <td><?php echo formatPrice($order['total_amount']); ?></td>
                                            <td class="status-cell">
                                                <span class="status-badge status-<?php echo $order['status']; ?>">
                                                    <?php echo ucfirst($order['status']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <a href="view_order.php?id=<?php echo $order['id']; ?>" class="action-btn edit-btn">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p>No orders found.</p>
                    <?php endif; ?>
                </div>

                <!-- Quick Links -->
                <div class="admin-card">
                    <div class="admin-card-header">
                        <h2 class="admin-card-title">Quick Actions</h2>
                    </div>
                    
                    <div style="display: flex; flex-wrap: wrap; gap: 15px;">
                        <a href="add_menu_item.php" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Add Menu Item
                        </a>
                        <a href="orders.php?status=pending" class="btn btn-secondary">
                            <i class="fas fa-clock"></i> View Pending Orders
                        </a>
                        <a href="messages.php" class="btn btn-secondary">
                            <i class="fas fa-envelope"></i> Check Messages
                        </a>
                    </div>
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