<?php
// Start session
session_start();

// Include functions
require_once __DIR__ . '/functions.php';

// Set the active page based on current URL
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Green Cafe - Organic & Fresh</title>
    <meta name="description" content="Green Cafe offers organic and fresh food in a cozy environment. Visit us for delicious meals and beverages.">
    
    <!-- Favicon -->
    <link rel="shortcut icon" href="<?php echo $currentPage === 'index.php' ? '' : '../'; ?>assets/images/favicon.ico" type="image/x-icon">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo $currentPage === 'index.php' ? '' : '../'; ?>assets/css/style.css">
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="container">
            <nav class="navbar">
                <a href="<?php echo $currentPage === 'index.php' ? '' : '../'; ?>index.php" class="logo">
                    Green Cafe
                </a>
                
                <ul class="nav-menu">
                    <li class="nav-item">
                        <a href="<?php echo $currentPage === 'index.php' ? '' : '../'; ?>index.php" class="nav-link <?php echo $currentPage === 'index.php' ? 'active' : ''; ?>">Home</a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo $currentPage === 'index.php' ? 'pages/' : ''; ?>menu.php" class="nav-link <?php echo $currentPage === 'menu.php' ? 'active' : ''; ?>">Menu</a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo $currentPage === 'index.php' ? 'pages/' : ''; ?>contact.php" class="nav-link <?php echo $currentPage === 'contact.php' ? 'active' : ''; ?>">Contact</a>
                    </li>
                </ul>
                
                <div class="hamburger">
                    <span class="bar"></span>
                    <span class="bar"></span>
                    <span class="bar"></span>
                </div>
            </nav>
        </div>
    </header> 