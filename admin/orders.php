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

// Handle status filter
$statusFilter = '';
$whereClause = '1=1';
if (isset($_GET['status']) && !empty($_GET['status'])) {
    $statusFilter = $_GET['status'];
    $whereClause = "status = '" . $conn->real_escape_string($statusFilter) . "'";
}

// Get all orders with customer details
$sql = "SELECT o.*, c.name as customer_name, c.email, c.phone
        FROM orders o 
        LEFT JOIN customers c ON o.customer_id = c.id 
        WHERE $whereClause
        ORDER BY o.order_date DESC";

$result = $conn->query($sql);
$orders = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $orders[] = $row;
    }
}

// Add CSS for customer info tooltip
$customCSS = "
.customer-info {
    position: relative;
    cursor: pointer;
}
.customer-info-tooltip {
    display: none;
    position: absolute;
    left: 0;
    top: 100%;
    background-color: white;
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    width: 250px;
    z-index: 10;
    font-size: 0.9rem;
}
.customer-info:hover .customer-info-tooltip {
    display: block;
}
.customer-detail {
    display: flex;
    margin-bottom: 5px;
}
.customer-detail i {
    width: 20px;
    color: #577e4c;
    margin-right: 5px;
}
";
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
    
    <style>
        .action-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border-radius: 4px;
            background-color: #f5f5f5;
            color: #333;
            transition: all 0.2s ease;
            text-decoration: none;
        }
        
        .action-btn:hover {
            transform: translateY(-2px);
        }
        
        .view-btn {
            background-color: #e3f2fd;
            color: #0d6efd;
        }
        
        .view-btn:hover {
            background-color: #0d6efd;
            color: white;
        }
        
        .status-btn {
            background-color: #e8f5e9;
            color: #28a745;
            border: none;
            cursor: pointer;
        }
        
        .status-btn:hover {
            background-color: #28a745;
            color: white;
        }
        
        <?php echo $customCSS; ?>
    </style>
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
                <!-- Orders Management -->
                <div class="admin-card">
                    <div class="admin-card-header">
                        <h2 class="admin-card-title">Manage Orders <?php echo !empty($statusFilter) ? '(' . ucfirst($statusFilter) . ')' : ''; ?></h2>
                        
                        <div class="filter-buttons" style="display: flex; gap: 10px;">
                            <a href="orders.php" class="btn <?php echo empty($statusFilter) ? 'btn-primary' : 'btn-secondary'; ?>">All</a>
                            <a href="orders.php?status=pending" class="btn <?php echo $statusFilter === 'pending' ? 'btn-primary' : 'btn-secondary'; ?>">Pending</a>
                            <a href="orders.php?status=processing" class="btn <?php echo $statusFilter === 'processing' ? 'btn-primary' : 'btn-secondary'; ?>">Processing</a>
                            <a href="orders.php?status=completed" class="btn <?php echo $statusFilter === 'completed' ? 'btn-primary' : 'btn-secondary'; ?>">Completed</a>
                            <a href="orders.php?status=cancelled" class="btn <?php echo $statusFilter === 'cancelled' ? 'btn-primary' : 'btn-secondary'; ?>">Cancelled</a>
                        </div>
                    </div>
                    
                    <?php if (count($orders) > 0): ?>
                        <div class="table-responsive">
                            <table class="admin-table">
                                <thead>
                                    <tr>
                                        <th width="80">Order #</th>
                                        <th width="180">Customer</th>
                                        <th width="140">Date</th>
                                        <th width="100">Amount</th>
                                        <th width="120">Status</th>
                                        <th width="100">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($orders as $order): ?>
                                        <tr>
                                            <td><?php echo $order['order_number']; ?></td>
                                            <td class="customer-info">
                                                <?php if (!empty($order['customer_name'])): ?>
                                                    <div style="display: flex; align-items: center;">
                                                        <i class="fas fa-user-circle" style="margin-right: 5px; color: #577e4c;"></i>
                                                        <span><?php echo $order['customer_name']; ?></span>
                                                    </div>
                                                    
                                                    <div class="customer-info-tooltip">
                                                        <div style="font-weight: bold; margin-bottom: 5px; color: #577e4c;">
                                                            Customer Information
                                                        </div>
                                                        
                                                        <div class="customer-detail">
                                                            <i class="fas fa-user"></i>
                                                            <span><?php echo $order['customer_name']; ?></span>
                                                        </div>
                                                        
                                                        <?php if (!empty($order['email'])): ?>
                                                            <div class="customer-detail">
                                                                <i class="fas fa-envelope"></i>
                                                                <span><?php echo $order['email']; ?></span>
                                                            </div>
                                                        <?php endif; ?>
                                                        
                                                        <?php if (!empty($order['phone'])): ?>
                                                            <div class="customer-detail">
                                                                <i class="fas fa-phone"></i>
                                                                <span><?php echo $order['phone']; ?></span>
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>
                                                <?php else: ?>
                                                    <div>
                                                        <i class="fas fa-user-circle" style="margin-right: 5px; color: #6c757d;"></i>
                                                        <span>Guest</span>
                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo date('M d, Y H:i', strtotime($order['order_date'])); ?></td>
                                            <td><?php echo formatPrice($order['total_amount']); ?></td>
                                            <td class="status-cell">
                                                <span class="status-badge status-<?php echo $order['status']; ?>">
                                                    <?php echo ucfirst($order['status']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="dropdown" style="position: relative; display: flex; gap: 8px; justify-content: center;">
                                                    <a href="view_order.php?id=<?php echo $order['id']; ?>" class="action-btn view-btn" title="View Order Details">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    
                                                    <button class="action-btn status-btn" id="statusBtn<?php echo $order['id']; ?>" title="Update Status">
                                                        <i class="fas fa-cog"></i>
                                                    </button>
                                                    
                                                    <div class="dropdown-content" id="statusDropdown<?php echo $order['id']; ?>" style="display: none; position: absolute; background-color: #fff; box-shadow: 0 2px 10px rgba(0,0,0,0.1); z-index: 1; min-width: 120px; border-radius: 4px; right: 0; top: 100%;">
                                                        <a href="update_order_status.php?id=<?php echo $order['id']; ?>&status=pending" style="display: block; padding: 10px; color: var(--dark); text-decoration: none;">Pending</a>
                                                        <a href="update_order_status.php?id=<?php echo $order['id']; ?>&status=processing" style="display: block; padding: 10px; color: var(--dark); text-decoration: none;">Processing</a>
                                                        <a href="update_order_status.php?id=<?php echo $order['id']; ?>&status=completed" style="display: block; padding: 10px; color: var(--dark); text-decoration: none;">Completed</a>
                                                        <a href="update_order_status.php?id=<?php echo $order['id']; ?>&status=cancelled" style="display: block; padding: 10px; color: var(--dark); text-decoration: none;">Cancelled</a>
                                                    </div>
                                                </div>
                                                
                                                <script>
                                                    document.addEventListener('DOMContentLoaded', function() {
                                                        const statusBtn<?php echo $order['id']; ?> = document.getElementById('statusBtn<?php echo $order['id']; ?>');
                                                        const statusDropdown<?php echo $order['id']; ?> = document.getElementById('statusDropdown<?php echo $order['id']; ?>');
                                                        
                                                        statusBtn<?php echo $order['id']; ?>.addEventListener('click', function(e) {
                                                            e.stopPropagation();
                                                            
                                                            // Close all other dropdowns
                                                            document.querySelectorAll('.dropdown-content').forEach(dropdown => {
                                                                if (dropdown.id !== 'statusDropdown<?php echo $order['id']; ?>') {
                                                                    dropdown.style.display = 'none';
                                                                }
                                                            });
                                                            
                                                            // Toggle current dropdown
                                                            if (statusDropdown<?php echo $order['id']; ?>.style.display === 'none') {
                                                                statusDropdown<?php echo $order['id']; ?>.style.display = 'block';
                                                            } else {
                                                                statusDropdown<?php echo $order['id']; ?>.style.display = 'none';
                                                            }
                                                        });
                                                        
                                                        document.addEventListener('click', function() {
                                                            document.querySelectorAll('.dropdown-content').forEach(dropdown => {
                                                                dropdown.style.display = 'none';
                                                            });
                                                        });
                                                    });
                                                </script>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p>No orders found<?php echo !empty($statusFilter) ? ' with status: ' . ucfirst($statusFilter) : ''; ?>.</p>
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