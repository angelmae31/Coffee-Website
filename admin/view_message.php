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

// Check if ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    redirect('messages.php');
}

$id = (int)$_GET['id'];

// Mark message as read
$updateSql = "UPDATE contact_messages SET is_read = 1 WHERE id = ?";
$stmt = $conn->prepare($updateSql);
$stmt->bind_param("i", $id);
$stmt->execute();

// Get message details
$sql = "SELECT * FROM contact_messages WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    redirect('messages.php');
}

$message = $result->fetch_assoc();
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
                <!-- Message Details -->
                <div class="admin-card">
                    <div class="admin-card-header">
                        <h2 class="admin-card-title">Message Details</h2>
                        <a href="messages.php" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Messages
                        </a>
                    </div>
                    
                    <div style="margin-bottom: 30px;">
                        <div style="background-color: var(--light-gray); padding: 20px; border-radius: 8px; margin-bottom: 20px;">
                            <div style="margin-bottom: 20px;">
                                <h3 style="font-size: 1.4rem; color: var(--green); margin-bottom: 5px;"><?php echo htmlspecialchars($message['subject']); ?></h3>
                                <p style="margin-bottom: 5px;"><strong>From:</strong> <?php echo htmlspecialchars($message['name']); ?> (<?php echo htmlspecialchars($message['email']); ?>)</p>
                                <p style="color: var(--medium-gray);"><strong>Received:</strong> <?php echo date('M d, Y H:i', strtotime($message['created_at'])); ?></p>
                            </div>
                            
                            <div style="line-height: 1.6;">
                                <p><?php echo nl2br(htmlspecialchars($message['message'])); ?></p>
                            </div>
                        </div>
                        
                        <div style="display: flex; gap: 10px;">
                            <a href="mailto:<?php echo htmlspecialchars($message['email']); ?>" class="btn btn-primary">
                                <i class="fas fa-reply"></i> Reply via Email
                            </a>
                            
                            <?php if ($message['is_read'] == 1): ?>
                                <span class="status-badge status-completed">Read</span>
                            <?php else: ?>
                                <span class="status-badge status-pending">Unread</span>
                            <?php endif; ?>
                        </div>
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