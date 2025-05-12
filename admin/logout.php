<?php
// Start session
session_start();

// Include functions file
require_once '../includes/functions.php';

// Unset all session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Redirect to login page
header("Location: login.php");
exit();
?> 