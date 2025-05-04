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

// Get all categories for filter dropdown
$categoriesResult = $conn->query("SELECT * FROM categories ORDER BY name ASC");
$categories = [];

if ($categoriesResult->num_rows > 0) {
    while ($row = $categoriesResult->fetch_assoc()) {
        $categories[] = $row;
    }
}

// Handle category filter
$categoryFilter = isset($_GET['category']) ? (int)$_GET['category'] : 0;
$whereClause = $categoryFilter > 0 ? "WHERE m.category_id = $categoryFilter" : "";

// Get menu items with categories (with filter if applied)
$sql = "SELECT m.*, c.name as category_name 
        FROM menu_items m 
        JOIN categories c ON m.category_id = c.id 
        $whereClause
        ORDER BY c.name, m.name ASC";

$result = $conn->query($sql);
$menuItems = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $menuItems[] = $row;
    }
}

// Handle available/featured status toggle
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $action = $_GET['action'];
    
    // Build redirect URL with filter if present
    $redirectUrl = "menu.php";
    if ($categoryFilter > 0) {
        $redirectUrl .= "?category=" . $categoryFilter . "&";
    } else {
        $redirectUrl .= "?";
    }
    
    if ($action === 'toggle_available') {
        // Toggle available status
        $sql = "UPDATE menu_items SET is_available = NOT is_available WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            $successMsg = "Menu item availability updated.";
            // Refresh the page to show updated data
            header("Location: " . $redirectUrl . "success=" . urlencode($successMsg));
            exit();
        } else {
            $errorMsg = "Error updating menu item: " . $conn->error;
        }
    } elseif ($action === 'toggle_featured') {
        // Toggle featured status
        $sql = "UPDATE menu_items SET is_featured = NOT is_featured WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            $successMsg = "Menu item featured status updated.";
            // Refresh the page to show updated data
            header("Location: " . $redirectUrl . "success=" . urlencode($successMsg));
            exit();
        } else {
            $errorMsg = "Error updating menu item: " . $conn->error;
        }
    } elseif ($action === 'delete' && isset($_GET['confirm']) && $_GET['confirm'] === 'yes') {
        // Delete menu item
        $sql = "DELETE FROM menu_items WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            $successMsg = "Menu item deleted successfully.";
            // Refresh the page to show updated data
            header("Location: " . $redirectUrl . "success=" . urlencode($successMsg));
            exit();
        } else {
            $errorMsg = "Error deleting menu item: " . $conn->error;
        }
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
        
        .edit-btn {
            background-color: #e3f2fd;
            color: #0d6efd;
        }
        
        .edit-btn:hover {
            background-color: #0d6efd;
            color: white;
        }
        
        .delete-btn {
            background-color: #ffebee;
            color: #dc3545;
        }
        
        .delete-btn:hover {
            background-color: #dc3545;
            color: white;
        }
        
        .action-buttons-container {
            display: flex;
            justify-content: center;
            gap: 8px;
        }
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
<!-- Success/Error Messages -->
<?php if (isset($_GET['success'])): ?>
    <div style="background-color: #d4edda; color: #155724; padding: 15px; border-radius: 4px; margin-bottom: 20px;">
        <?php echo htmlspecialchars($_GET['success']); ?>
    </div>
<?php endif; ?>

<?php if (isset($errorMsg)): ?>
    <div style="background-color: #f8d7da; color: #721c24; padding: 15px; border-radius: 4px; margin-bottom: 20px;">
        <?php echo $errorMsg; ?>
    </div>
<?php endif; ?>

<!-- Menu Management -->
<div class="admin-card">
    <div class="admin-card-header">
        <h2 class="admin-card-title">Manage Menu Items</h2>
                        <div style="display: flex; gap: 15px;">
                            <!-- Category Filter -->
                            <form method="get" action="menu.php" style="display: flex; align-items: center; gap: 10px;">
                                <label for="category" style="white-space: nowrap;">Filter by Category:</label>
                                <select name="category" id="category" class="form-control" style="width: auto;" onchange="this.form.submit()">
                                    <option value="0">All Categories</option>
                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?php echo $category['id']; ?>" <?php echo $categoryFilter == $category['id'] ? 'selected' : ''; ?>>
                                            <?php echo $category['name']; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </form>
        <a href="add_menu_item.php" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add Menu Item
        </a>
                        </div>
    </div>
    
    <?php if (count($menuItems) > 0): ?>
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                                        <th style="width: 50px;">#</th>
                                        <th style="width: 70px;">Image</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Available</th>
                        <th>Featured</th>
                                        <th style="width: 100px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                                    <?php $rowNumber = 1; ?>
                    <?php foreach ($menuItems as $item): ?>
                        <tr>
                                            <td style="text-align: center;"><?php echo $rowNumber++; ?></td>
                                            <td>
                                                <?php if (!empty($item['image'])): ?>
                                                    <div style="width: 50px; height: 50px; overflow: hidden; border-radius: 4px;">
                                                        <img src="../assets/images/menu/<?php echo $item['image']; ?>" 
                                                             alt="<?php echo $item['name']; ?>" 
                                                             style="width: 100%; height: 100%; object-fit: cover;">
                                                    </div>
                                                <?php else: ?>
                                                    <div style="width: 50px; height: 50px; background-color: #f0f0f0; border-radius: 4px; display: flex; align-items: center; justify-content: center;">
                                                        <i class="fas fa-image" style="color: #aaa;"></i>
                                                    </div>
                                                <?php endif; ?>
                                            </td>
                            <td><?php echo $item['name']; ?></td>
                            <td><?php echo $item['category_name']; ?></td>
                            <td><?php echo formatPrice($item['price']); ?></td>
                            <td>
                                                <a href="menu.php?<?php echo $categoryFilter > 0 ? 'category=' . $categoryFilter . '&' : ''; ?>action=toggle_available&id=<?php echo $item['id']; ?>" 
                                   class="status-badge <?php echo $item['is_available'] ? 'status-completed' : 'status-cancelled'; ?>">
                                    <?php echo $item['is_available'] ? 'Yes' : 'No'; ?>
                                </a>
                            </td>
                            <td>
                                                <a href="menu.php?<?php echo $categoryFilter > 0 ? 'category=' . $categoryFilter . '&' : ''; ?>action=toggle_featured&id=<?php echo $item['id']; ?>"
                                   class="status-badge <?php echo $item['is_featured'] ? 'status-completed' : 'status-pending'; ?>">
                                    <?php echo $item['is_featured'] ? 'Yes' : 'No'; ?>
                                </a>
                            </td>
                                            <td style="text-align: center;">
                                                <div class="action-buttons-container">
                                                    <a href="edit_menu_item.php?id=<?php echo $item['id']; ?>" class="action-btn edit-btn" title="Edit Item">
                                    <i class="fas fa-edit"></i>
                                </a>
                                                    <a href="menu.php?<?php echo $categoryFilter > 0 ? 'category=' . $categoryFilter . '&' : ''; ?>action=delete&id=<?php echo $item['id']; ?>&confirm=yes" 
                                   class="action-btn delete-btn" 
                                                       title="Delete Item"
                                   onclick="return confirm('Are you sure you want to delete this menu item?')">
                                    <i class="fas fa-trash"></i>
                                </a>
                                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
                        <div style="margin-top: 15px; color: #6c757d;">
                            Showing <?php echo count($menuItems); ?> item<?php echo count($menuItems) != 1 ? 's' : ''; ?>
                            <?php echo $categoryFilter > 0 ? 'in ' . $categories[array_search($categoryFilter, array_column($categories, 'id'))]['name'] . ' category' : ''; ?>
                        </div>
                    <?php else: ?>
                        <p>
                            <?php if ($categoryFilter > 0): ?>
                                No menu items found in this category. <a href="menu.php">View all categories</a> or <a href="add_menu_item.php">add a new menu item</a>.
    <?php else: ?>
                                No menu items found. <a href="add_menu_item.php">Add your first menu item</a>.
                            <?php endif; ?>
                        </p>
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