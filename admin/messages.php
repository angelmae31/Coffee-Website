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

// Handle read/unread toggle
if (isset($_GET['action']) && $_GET['action'] === 'toggle_read' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    
    // Toggle read status
    $sql = "UPDATE contact_messages SET is_read = NOT is_read WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        $successMsg = "Message status updated.";
    } else {
        $errorMsg = "Error updating message status: " . $conn->error;
    }
}

// Handle message deletion
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    
    // Delete message
    $sql = "DELETE FROM contact_messages WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        $successMsg = "Message deleted successfully.";
    } else {
        $errorMsg = "Error deleting message: " . $conn->error;
    }
}

// Get messages
$sql = "SELECT * FROM contact_messages ORDER BY created_at DESC";
$result = $conn->query($sql);
$messages = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $messages[] = $row;
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

                <!-- Messages Management -->
                <div class="admin-card">
                    <div class="admin-card-header">
                        <h2 class="admin-card-title">Customer Messages</h2>
                    </div>
                    
                    <?php if (count($messages) > 0): ?>
                        <div class="table-responsive">
                            <table class="admin-table">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Subject</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($messages as $message): ?>
                                        <tr class="<?php echo $message['is_read'] ? '' : 'unread-row'; ?>" style="<?php echo $message['is_read'] ? '' : 'font-weight: bold; background-color: rgba(87, 126, 76, 0.05);'; ?>">
                                            <td><?php echo htmlspecialchars($message['name']); ?></td>
                                            <td><?php echo htmlspecialchars($message['email']); ?></td>
                                            <td><?php echo htmlspecialchars($message['subject'] ?: 'No Subject'); ?></td>
                                            <td><?php echo date('M d, Y H:i', strtotime($message['created_at'])); ?></td>
                                            <td>
                                                <span class="status-badge <?php echo $message['is_read'] ? 'status-completed' : 'status-pending'; ?>">
                                                    <?php echo $message['is_read'] ? 'Read' : 'Unread'; ?>
                                                </span>
                                            </td>
                                            <td>
                                                <a href="view_message.php?id=<?php echo $message['id']; ?>" class="action-btn edit-btn" title="View">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="messages.php?action=toggle_read&id=<?php echo $message['id']; ?>" class="action-btn <?php echo $message['is_read'] ? 'status-pending' : 'status-completed'; ?>" style="background-color: <?php echo $message['is_read'] ? '#ffc107' : '#28a745'; ?>;" title="<?php echo $message['is_read'] ? 'Mark as Unread' : 'Mark as Read'; ?>">
                                                    <i class="fas <?php echo $message['is_read'] ? 'fa-envelope' : 'fa-envelope-open'; ?>"></i>
                                                </a>
                                                <a href="messages.php?action=delete&id=<?php echo $message['id']; ?>" class="action-btn delete-btn" title="Delete" onclick="return confirm('Are you sure you want to delete this message?')">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p>No messages found.</p>
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