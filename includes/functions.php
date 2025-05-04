<?php
/**
 * Helper Functions
 * 
 * Contains utility functions used throughout the Cafe website
 */

// Include database connection
require_once __DIR__ . '/../config/db.php';

/**
 * Sanitize user input to prevent XSS
 * 
 * @param string $data The input to sanitize
 * @return string Sanitized input
 */
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

/**
 * Redirect to a specific URL
 * 
 * @param string $url The URL to redirect to
 * @return void
 */
function redirect($url) {
    header("Location: $url");
    exit();
}

/**
 * Check if user is logged in
 * 
 * @return bool True if user is logged in, false otherwise
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Check if user is an admin
 * 
 * @return bool True if user is an admin, false otherwise
 */
function isAdmin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

/**
 * Get all menu categories
 * 
 * @return array Array of category records
 */
function getAllCategories() {
    $conn = getDBConnection();
    $sql = "SELECT * FROM categories ORDER BY name ASC";
    $result = $conn->query($sql);
    
    $categories = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $categories[] = $row;
        }
    }
    
    return $categories;
}

/**
 * Get all menu items or items by category
 * 
 * @param int|null $categoryId Optional category ID to filter by
 * @return array Array of menu item records
 */
function getMenuItems($categoryId = null) {
    $conn = getDBConnection();
    
    if ($categoryId) {
        $stmt = $conn->prepare("SELECT * FROM menu_items WHERE category_id = ? AND is_available = 1 ORDER BY name ASC");
        $stmt->bind_param("i", $categoryId);
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
        $sql = "SELECT m.*, c.name as category_name 
                FROM menu_items m 
                JOIN categories c ON m.category_id = c.id 
                WHERE m.is_available = 1 
                ORDER BY c.name, m.name ASC";
        $result = $conn->query($sql);
    }
    
    $menuItems = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $menuItems[] = $row;
        }
    }
    
    return $menuItems;
}

/**
 * Get featured menu items
 * 
 * @param int $limit Maximum number of items to return
 * @return array Array of featured menu items
 */
function getFeaturedItems($limit = 6) {
    $conn = getDBConnection();
    
    $sql = "SELECT m.*, c.name as category_name 
            FROM menu_items m 
            JOIN categories c ON m.category_id = c.id 
            WHERE m.is_featured = 1 AND m.is_available = 1 
            ORDER BY RAND() 
            LIMIT ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $limit);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $featuredItems = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $featuredItems[] = $row;
        }
    }
    
    return $featuredItems;
}

/**
 * Get a single menu item by ID
 * 
 * @param int $id The menu item ID
 * @return array|null The menu item record or null if not found
 */
function getMenuItem($id) {
    $conn = getDBConnection();
    
    $stmt = $conn->prepare("SELECT m.*, c.name as category_name 
                           FROM menu_items m 
                           JOIN categories c ON m.category_id = c.id 
                           WHERE m.id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        return $result->fetch_assoc();
    }
    
    return null;
}

/**
 * Format price as currency
 * 
 * @param float $price The price to format
 * @return string Formatted price
 */
function formatPrice($price) {
    return '$' . number_format($price, 2);
}

/**
 * Submit a contact form message
 * 
 * @param string $name Sender's name
 * @param string $email Sender's email
 * @param string $subject Message subject
 * @param string $message Message content
 * @return bool True if successful, false otherwise
 */
function submitContactMessage($name, $email, $subject, $message) {
    $conn = getDBConnection();
    
    $stmt = $conn->prepare("INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $email, $subject, $message);
    
    return $stmt->execute();
}

/**
 * Get recent orders
 * 
 * @param int $limit Maximum number of orders to return
 * @return array Array of recent orders
 */
function getRecentOrders($limit = 10) {
    $conn = getDBConnection();
    
    $sql = "SELECT o.*, c.name as customer_name 
            FROM orders o 
            LEFT JOIN customers c ON o.customer_id = c.id 
            ORDER BY o.order_date DESC 
            LIMIT ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $limit);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $orders = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $orders[] = $row;
        }
    }
    
    return $orders;
} 