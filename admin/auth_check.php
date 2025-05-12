<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Get admin information
$admin_id = $_SESSION['admin_id'];
$admin_username = $_SESSION['admin_username'];
?> 