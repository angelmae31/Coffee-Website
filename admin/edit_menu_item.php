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
if (!isset($_GET['id'])) {
    redirect('menu.php');
}

$id = (int)$_GET['id'];

// Get menu item details
$stmt = $conn->prepare("SELECT * FROM menu_items WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    redirect('menu.php');
}

$item = $result->fetch_assoc();

// Get all categories for the dropdown
$categoriesResult = $conn->query("SELECT * FROM categories ORDER BY name ASC");
$categories = [];

if ($categoriesResult->num_rows > 0) {
    while ($row = $categoriesResult->fetch_assoc()) {
        $categories[] = $row;
    }
}

// Initialize variables
$name = $item['name'];
$description = $item['description'];
$price = $item['price'];
$category_id = $item['category_id'];
$is_featured = $item['is_featured'];
$is_available = $item['is_available'];
$current_image = $item['image'];
$nameErr = $priceErr = $categoryErr = '';
$successMsg = $errorMsg = '';

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate and sanitize input
    $name = sanitizeInput($_POST['name']);
    $description = sanitizeInput($_POST['description']);
    $price = (float)sanitizeInput($_POST['price']);
    $category_id = (int)sanitizeInput($_POST['category_id']);
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    $is_available = isset($_POST['is_available']) ? 1 : 0;
    
    // Validate fields
    $isValid = true;
    
    if (empty($name)) {
        $nameErr = 'Name is required';
        $isValid = false;
    }
    
    if ($price <= 0) {
        $priceErr = 'Price must be greater than 0';
        $isValid = false;
    }
    
    if ($category_id <= 0) {
        $categoryErr = 'Category is required';
        $isValid = false;
    }
    
    // If valid, update database
    if ($isValid) {
        $image = $current_image; // Keep existing image by default
        
        // Handle image upload if provided
        if ($_FILES['image']['size'] > 0 && $_FILES['image']['error'] == 0) {
            $filename = $_FILES['image']['name'];
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];
            
            if (in_array($ext, $allowed)) {
                $newFilename = uniqid() . '.' . $ext;
                $uploadDir = '../assets/images/menu/';
                
                // Create directory if it doesn't exist
                if (!file_exists($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                
                $uploadPath = $uploadDir . $newFilename;
                
                if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath)) {
                    // Delete old image if exists
                    if (!empty($current_image) && file_exists($uploadDir . $current_image)) {
                        unlink($uploadDir . $current_image);
                    }
                    $image = $newFilename;
                } else {
                    $errorMsg = 'Failed to upload image';
                }
            } else {
                $errorMsg = 'Invalid image format. Only JPG, JPEG, PNG, and GIF are allowed.';
            }
        }
        
        // If no error with image upload, update the menu item
        if (empty($errorMsg)) {
            $sql = "UPDATE menu_items SET category_id = ?, name = ?, description = ?, price = ?, image = ?, is_featured = ?, is_available = ? WHERE id = ?";
            
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("issdssii", $category_id, $name, $description, $price, $image, $is_featured, $is_available, $id);
            
            if ($stmt->execute()) {
                $successMsg = 'Menu item updated successfully!';
            } else {
                $errorMsg = 'Error updating menu item: ' . $conn->error;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Menu Item - Green Cafe</title>
    
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
        <!-- Sidebar -->
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
        
        <!-- Main Content -->
        <div class="admin-content">
            <header class="admin-header">
                <h1 class="admin-heading">Edit Menu Item</h1>
                <div>
                    <span>Welcome, <?php echo $_SESSION['username']; ?></span>
                </div>
            </header>
            
            <div class="admin-content-inner" style="padding: 20px;">
                <!-- Success/Error Messages -->
                <?php if (!empty($successMsg)): ?>
                    <div style="background-color: #d4edda; color: #155724; padding: 15px; border-radius: 4px; margin-bottom: 20px;">
                        <?php echo $successMsg; ?>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($errorMsg)): ?>
                    <div style="background-color: #f8d7da; color: #721c24; padding: 15px; border-radius: 4px; margin-bottom: 20px;">
                        <?php echo $errorMsg; ?>
                    </div>
                <?php endif; ?>
                
                <div class="admin-card">
                    <div class="admin-card-header">
                        <h2 class="admin-card-title">Edit Menu Item</h2>
                    </div>
                    
                    <form method="post" enctype="multipart/form-data" style="max-width: 600px;">
                        <div class="form-group">
                            <label for="name">Name*</label>
                            <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($name); ?>" required>
                            <?php if (!empty($nameErr)): ?>
                                <span style="color: #dc3545;"><?php echo $nameErr; ?></span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="form-group">
                            <label for="category_id">Category*</label>
                            <select class="form-control" id="category_id" name="category_id" required>
                                <option value="">Select Category</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?php echo $category['id']; ?>" <?php echo $category_id == $category['id'] ? 'selected' : ''; ?>>
                                        <?php echo $category['name']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (!empty($categoryErr)): ?>
                                <span style="color: #dc3545;"><?php echo $categoryErr; ?></span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="4"><?php echo htmlspecialchars($description); ?></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label for="price">Price*</label>
                            <input type="number" class="form-control" id="price" name="price" value="<?php echo $price; ?>" step="0.01" min="0" required>
                            <?php if (!empty($priceErr)): ?>
                                <span style="color: #dc3545;"><?php echo $priceErr; ?></span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="form-group">
                            <label>Current Image</label>
                            <?php if (!empty($current_image)): ?>
                                <div style="margin: 10px 0;">
                                    <img src="../assets/images/menu/<?php echo $current_image; ?>" alt="<?php echo $name; ?>" style="max-width: 200px; border-radius: 4px;">
                                </div>
                            <?php else: ?>
                                <p>No image uploaded</p>
                            <?php endif; ?>
                        </div>
                        
                        <div class="form-group">
                            <label for="image">New Image</label>
                            <input type="file" class="form-control" id="image" name="image" accept="image/*">
                            <small class="text-muted">Optional. Leave empty to keep current image. Recommended size: 800x600px. Max file size: 2MB.</small>
                        </div>
                        
                        <div class="form-group" style="display: flex; gap: 20px; margin-top: 20px;">
                            <div>
                                <input type="checkbox" id="is_featured" name="is_featured" <?php echo $is_featured ? 'checked' : ''; ?>>
                                <label for="is_featured">Featured Item</label>
                            </div>
                            
                            <div>
                                <input type="checkbox" id="is_available" name="is_available" <?php echo $is_available ? 'checked' : ''; ?>>
                                <label for="is_available">Available</label>
                            </div>
                        </div>
                        
                        <div style="margin-top: 30px;">
                            <button type="submit" class="btn btn-primary">Update Menu Item</button>
                            <a href="menu.php" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <script src="../assets/js/main.js"></script>
</body>
</html> 